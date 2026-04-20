<?php
namespace App\Http\Controllers;

use App\Models\{Order, Rider, Delivery, User, Courier};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    private function getCourier()
    {
        return auth()->user()->courierAccount;
    }

    public function dashboard()
    {
        $courier = $this->getCourier();
        if (!$courier) abort(403, 'No courier account associated with this user.');

        $stats = [
            'unassigned'  => Order::where('courier_service', $courier->code)
                                  ->whereNull('rider_id')
                                  ->where('status', 'shipped')
                                  ->count(),
            'in_transit'  => Delivery::whereHas('order', fn($q) => $q->where('courier_service', $courier->code))
                                     ->whereIn('status', ['picked_up', 'out_for_delivery'])
                                     ->count(),
            'delivered'   => Delivery::whereHas('order', fn($q) => $q->where('courier_service', $courier->code))
                                     ->where('status', 'delivered')
                                     ->whereDate('delivered_at', today())
                                     ->count(),
            'active_riders' => Rider::where('courier_id', $courier->id)
                                    ->where('is_available', true)
                                    ->count(),
        ];

        $pendingOrders = Order::where('courier_service', $courier->code)
                              ->whereIn('status', ['processing', 'shipped'])
                              ->whereNull('rider_id')
                              ->with(['user'])
                              ->latest()
                              ->get();

        $riders = Rider::where('courier_id', $courier->id)
                       ->with(['user', 'activeDeliveries'])
                       ->get();

        return view('courier.dashboard', compact('stats', 'pendingOrders', 'riders', 'courier'));
    }

    public function orders(Request $request)
    {
        $courier = $this->getCourier();
        $query = Order::where('courier_service', $courier->code)->with(['user', 'rider.user']);

        // Handle filtering
        if ($request->status === 'unassigned') {
            $query->whereNull('rider_id');
        } elseif ($request->status === 'in_transit') {
            $query->whereIn('status', ['shipped', 'out_for_delivery'])->whereNotNull('rider_id');
        } elseif ($request->status === 'delivered') {
            $query->where('status', 'delivered');
        }

        $orders = $query->latest()->paginate(10);
        return view('courier.orders', compact('orders', 'courier'));
    }

    public function show(Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $order->load(['user', 'rider.user', 'delivery']);
        $riders = Rider::where('courier_id', $courier->id)->with('user')->get();

        return view('courier.orders.show', compact('order', 'riders', 'courier'));
    }

    public function assignRider(Request $request, Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $request->validate([
            'rider_id' => 'required|exists:riders,id',
        ]);

        $rider = Rider::where('id', $request->rider_id)
                      ->where('courier_id', $courier->id)
                      ->firstOrFail();

        $delivery = DB::transaction(function () use ($order, $rider) {
            $order->update(['rider_id' => $rider->id]);

            return Delivery::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'rider_id'    => $rider->id,
                    'status'      => 'assigned',
                    'assigned_at' => now(),
                ]
            );
        });

        // Fire Real-time events
        broadcast(new \App\Events\RiderAssigned($delivery))->toOthers();
        broadcast(new \App\Events\OrderStatusUpdated($order, "Rider assigned to your order."))->toOthers();

        return back()->with('success', 'Rider assigned successfully to Order #' . $order->id);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $request->validate([
            'status' => 'required|in:shipped,released'
        ]);

        try {
            if ($request->status === 'released') {
                if ($order->delivery) {
                    $order->delivery->update(['released_at' => now()]);
                    return back()->with('success', 'Package released to rider. Waiting for rider confirmation.');
                }
                return back()->with('error', 'No delivery record found for this order.');
            }

            $orderService = app(\App\Services\OrderService::class);
            $orderService->updateStatus($order, $request->status, 'courier');

            // Sync delivery status if it exists
            if ($order->delivery) {
                $order->delivery->update(['status' => $request->status]);
            }

            return back()->with('success', 'Status updated to ' . str_replace('_', ' ', $request->status));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reportLost(Request $request, Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($order, $request) {
            $order->update(['status' => 'lost']);
            
            if ($order->delivery) {
                $order->delivery->update([
                    'status' => 'lost',
                    'lost_at' => now(),
                    'lost_reason' => $request->reason
                ]);
            } else {
                Delivery::create([
                    'order_id' => $order->id,
                    'status' => 'lost',
                    'lost_at' => now(),
                    'lost_reason' => $request->reason
                ]);
            }

            // Fire real-time events
            broadcast(new \App\Events\PackageLostReported($order, $request->reason))->toOthers();
            broadcast(new \App\Events\OrderStatusUpdated($order, "Issue detected with your delivery. Admin will contact you."))->toOthers();
        });

        return back()->with('success', 'Package reported as lost. Admin has been notified.');
    }

    public function riders()
    {
        $courier = $this->getCourier();
        $riders = Rider::where('courier_id', $courier->id)
                       ->with(['user', 'deliveries'])
                       ->get();

        return view('courier.riders', compact('riders', 'courier'));
    }

    public function storeRider(Request $request)
    {
        $courier = $this->getCourier();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'rider',
        ]);

        Rider::create([
            'user_id'    => $user->id,
            'courier_id' => $courier->id,
            'phone'      => $request->phone,
        ]);

        return back()->with('success', 'Rider ' . $request->name . ' added successfully.');
    }
}
