<?php

/**
 * FILE: CourierController.php
 * 
 * What this file does:
 * This controller handles everything for the "Courier Portal" (the dashboard for 
 * delivery companies like J&T or LBC). It allows the courier to manage their 
 * riders, assign orders to them, and report issues like lost packages.
 * 
 * How it connects to the project:
 * - It is called by routes starting with "/courier" in routes/web.php.
 * - It uses the Order, Rider, and Delivery models.
 * - It allows the courier to manage the middle part of the delivery chain.
 */

namespace App\Http\Controllers;

use App\Models\{Order, Rider, Delivery, User, Courier};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    /**
     * Helper function to get the Courier profile of the logged-in user.
     */
    private function getCourier()
    {
        return auth()->user()->courierAccount;
    }

    /**
     * Shows the Courier Dashboard with statistics and pending orders.
     * 
     * @return view — the courier dashboard
     */
    public function dashboard()
    {
        $courier = $this->getCourier();
        // SECURITY: Ensure this user actually has a courier account.
        if (!$courier) abort(403, 'No courier account associated with this user.');

        // Calculate statistics for the dashboard cards.
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

        // Get orders that are ready to be picked up by a rider.
        $pendingOrders = Order::where('courier_service', $courier->code)
                              ->whereIn('status', ['processing', 'shipped'])
                              ->whereNull('rider_id')
                              ->with(['user'])
                              ->latest()
                              ->get();

        // Get the list of all riders working for this courier.
        $riders = Rider::where('courier_id', $courier->id)
                       ->with(['user', 'activeDeliveries'])
                       ->get();

        return view('courier.dashboard', compact('stats', 'pendingOrders', 'riders', 'courier'));
    }

    /**
     * Shows a list of all orders assigned to this courier.
     * 
     * @param Request $request — contains filters for Unassigned or In Transit orders
     * @return view — the orders list page
     */
    public function orders(Request $request)
    {
        $courier = $this->getCourier();
        $query = Order::where('courier_service', $courier->code)->with(['user', 'rider.user']);

        // Handle filtering based on user selection.
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

    /**
     * Shows the full details of a specific order.
     * 
     * @param Order $order — the order model
     * @return view — the order details page
     */
    public function show(Order $order)
    {
        $courier = $this->getCourier();
        // SECURITY: Ensure the courier is only looking at their own assigned orders.
        abort_if($order->courier_service !== $courier->code, 403);

        $order->load(['user', 'rider.user', 'delivery']);
        $riders = Rider::where('courier_id', $courier->id)->with('user')->get();

        return view('courier.orders.show', compact('order', 'riders', 'courier'));
    }

    /**
     * Assigns a specific Rider to deliver an order.
     * 
     * @param Request $request — contains the rider ID
     * @param Order $order — the order to assign
     * @return redirect — back with success message
     */
    public function assignRider(Request $request, Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $request->validate([
            'rider_id' => 'required|exists:riders,id',
        ]);

        // Ensure the rider actually works for this courier.
        $rider = Rider::where('id', $request->rider_id)
                      ->where('courier_id', $courier->id)
                      ->firstOrFail();

        // Use a transaction to update both the Order and the Delivery record.
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

        // BROADCAST: Notify the rider's phone app that they have a new delivery.
        broadcast(new \App\Events\RiderAssigned($delivery))->toOthers();
        // BROADCAST: Notify the customer that a rider is coming.
        broadcast(new \App\Events\OrderStatusUpdated($order, "Rider assigned to your order."))->toOthers();

        return back()->with('success', 'Rider assigned successfully to Order #' . $order->id);
    }

    /**
     * Updates the status of a package (e.g., Shipped or Released).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $request->validate([
            'status' => 'required|in:shipped,released'
        ]);

        try {
            // "Released" means the courier has handed the physical box to the rider.
            if ($request->status === 'released') {
                if ($order->delivery) {
                    $order->delivery->update(['released_at' => now()]);
                    return back()->with('success', 'Package released to rider. Waiting for rider confirmation.');
                }
                return back()->with('error', 'No delivery record found for this order.');
            }

            // Use the OrderService for standard status transitions.
            $orderService = app(\App\Services\OrderService::class);
            $orderService->updateStatus($order, $request->status, 'courier');

            if ($order->delivery) {
                $order->delivery->update(['status' => $request->status]);
            }

            return back()->with('success', 'Status updated to ' . str_replace('_', ' ', $request->status));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reports a package as "Lost" if something went wrong during delivery.
     * 
     * @param Request $request — contains the reason why it was lost
     */
    public function reportLost(Request $request, Order $order)
    {
        $courier = $this->getCourier();
        abort_if($order->courier_service !== $courier->code, 403);

        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        DB::transaction(function () use ($order, $request) {
            // STEP 1: Mark the order as lost in the database.
            $order->update(['status' => 'lost']);
            
            // STEP 2: Update the delivery record with the date and reason.
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

            // BROADCAST: Alert the admin dashboard about the lost package.
            broadcast(new \App\Events\PackageLostReported($order, $request->reason))->toOthers();
            // BROADCAST: Notify the customer that there is an issue with their delivery.
            broadcast(new \App\Events\OrderStatusUpdated($order, "Issue detected with your delivery. Admin will contact you."))->toOthers();
        });

        return back()->with('success', 'Package reported as lost. Admin has been notified.');
    }

    /**
     * Shows the list of riders working for this courier.
     */
    public function riders()
    {
        $courier = $this->getCourier();
        $riders = Rider::where('courier_id', $courier->id)
                       ->with(['user', 'deliveries'])
                       ->get();

        return view('courier.riders', compact('riders', 'courier'));
    }

    /**
     * Registers a new Rider under this courier company.
     * 
     * @param Request $request — contains rider's name, email, phone, and password
     */
    public function storeRider(Request $request)
    {
        $courier = $this->getCourier();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // STEP 1: Create the User account.
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'rider',
        ]);

        // STEP 2: Create the Rider profile linked to this courier.
        Rider::create([
            'user_id'    => $user->id,
            'courier_id' => $courier->id,
            'phone'      => $request->phone,
        ]);

        return back()->with('success', 'Rider ' . $request->name . ' added successfully.');
    }
}
