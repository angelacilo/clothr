<?php

/**
 * FILE: RiderController.php
 * 
 * What this file does:
 * This controller is for the "Rider App/Portal". It allows individual delivery 
 * riders to manage their assigned packages. They can see what they need to pick 
 * up, mark packages as "In Transit", and upload a photo as Proof of Delivery (POD) 
 * when the item is finally handed to the customer.
 * 
 * How it connects to the project:
 * - It is called by routes starting with "/rider" in routes/web.php.
 * - It uses the Delivery and Rider models.
 * - It closes the delivery loop by marking orders as "Delivered".
 */

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    /**
     * Helper function to get the Rider profile of the logged-in user.
     */
    private function getRider()
    {
        return auth()->user()->rider;
    }

    /**
     * Shows the Rider Dashboard with their current work stats.
     * 
     * @return view — the rider dashboard
     */
    public function dashboard()
    {
        $rider = $this->getRider();
        // SECURITY: Ensure this user actually has a rider account.
        if (!$rider) abort(403, 'No rider account associated with this user.');

        // Statistics for the dashboard summary cards.
        $stats = [
            'assigned'   => Delivery::where('rider_id', $rider->id)->where('status', 'assigned')->count(),
            'in_transit' => Delivery::where('rider_id', $rider->id)->whereIn('status', ['picked_up', 'out_for_delivery'])->count(),
            'delivered'  => Delivery::where('rider_id', $rider->id)->where('status', 'delivered')->whereDate('delivered_at', today())->count(),
        ];

        // Get the list of deliveries that are currently active (not finished yet).
        $activeDeliveries = Delivery::where('rider_id', $rider->id)
                                    ->whereNotIn('status', ['delivered', 'failed'])
                                    ->with(['order.user'])
                                    ->latest()
                                    ->get();

        return view('rider.dashboard', compact('stats', 'activeDeliveries', 'rider'));
    }

    /**
     * Shows a full history of all deliveries made by this rider.
     */
    public function deliveries()
    {
        $rider = $this->getRider();
        $deliveries = Delivery::where('rider_id', $rider->id)
                              ->with(['order.user'])
                              ->latest()
                              ->paginate(20);

        return view('rider.deliveries', compact('deliveries', 'rider'));
    }

    /**
     * Shows the details of a single delivery (address, phone, customer name).
     */
    public function show(Delivery $delivery)
    {
        $rider = $this->getRider();
        // SECURITY: A rider can only see their own assigned deliveries.
        abort_if($delivery->rider_id !== $rider->id, 403);
        
        $delivery->load(['order.user']);
        return view('rider.deliveries.show', compact('delivery', 'rider'));
    }

    /**
     * Updates the status of the delivery (Picked Up -> Out for Delivery -> Delivered).
     * 
     * @param Request $request — contains the new status and optional photo
     */
    public function updateStatus(Request $request, Delivery $delivery)
    {
        $rider = $this->getRider();
        abort_if($delivery->rider_id !== $rider->id, 403);

        // VALIDATION: If marking as "Delivered", a photo (proof of delivery) is REQUIRED.
        $request->validate([
            'status' => 'required|in:picked_up,out_for_delivery,delivered',
            'proof_of_delivery' => 'required_if:status,delivered|image|max:5120', // 5MB max limit.
        ]);

        try {
            // Use a transaction to ensure both the Order and Delivery records update together.
            DB::transaction(function () use ($request, $delivery, $rider) {
                $orderService = app(\App\Services\OrderService::class);
                
                // STEP 1: Update the main Order status (Standard logic).
                $orderService->updateStatus($delivery->order, $request->status, 'rider');

                $update = ['status' => $request->status];
                
                // STEP 2: Save the Proof of Delivery photo if uploaded.
                if ($request->hasFile('proof_of_delivery')) {
                    $path = $request->file('proof_of_delivery')->store('proofs', 'public');
                    $update['proof_of_delivery'] = $path;
                }

                // STEP 3: Handle specific timestamps.
                if ($request->status === 'picked_up') {
                    $update['picked_up_at'] = now();
                    
                    // NOTIFY COURIER: Tell the delivery company that the rider has the package.
                    $courier = $rider->courier;
                    if ($courier && $courier->user_id) {
                        \App\Models\UserNotification::notify(
                            $courier->user_id,
                            $delivery->order->id,
                            'rider_picked_up',
                            'Package Picked Up',
                            "Rider {$rider->user->name} successfully picked up package for Order #{$delivery->order->id}.",
                            "/courier/orders/{$delivery->order->id}"
                        );
                    }
                }
                
                if ($request->status === 'delivered') {
                    $update['delivered_at'] = now();
                }

                $delivery->update($update);
            });

            return back()->with('success', 'Delivery status updated to ' . str_replace('_', ' ', $request->status) . '.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Toggles the rider's availability (On Duty / Off Duty).
     * 
     * WHY: Couriers shouldn't assign orders to riders who are currently resting.
     */
    public function toggleAvailability()
    {
        $rider = $this->getRider();
        $rider->update(['is_available' => !$rider->is_available]);
        
        $status = $rider->is_available ? 'Available' : 'Unavailable';
        return back()->with('success', 'You are now marked as ' . $status . '.');
    }
}
