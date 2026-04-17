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
                              ->whereNull('rider_id')
                              ->where('status', 'shipped')
                              ->with(['user'])
                              ->latest()
                              ->get();

        $riders = Rider::where('courier_id', $courier->id)
                       ->with(['user', 'activeDeliveries'])
                       ->get();

        return view('courier.dashboard', compact('stats', 'pendingOrders', 'riders', 'courier'));
    }

    public function orders()
    {
        $courier = $this->getCourier();
        $orders = Order::where('courier_service', $courier->code)
                       ->with(['user', 'rider.user', 'delivery'])
                       ->latest()
                       ->paginate(20);

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

        DB::transaction(function () use ($order, $rider) {
            $order->update(['rider_id' => $rider->id]);

            Delivery::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'rider_id'    => $rider->id,
                    'status'      => 'assigned',
                    'assigned_at' => now(),
                ]
            );
        });

        return back()->with('success', 'Rider assigned successfully to Order #' . $order->id);
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
