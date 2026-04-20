<?php
namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    private function getRider()
    {
        return auth()->user()->rider;
    }

    public function dashboard()
    {
        $rider = $this->getRider();
        if (!$rider) abort(403, 'No rider account associated with this user.');

        $stats = [
            'assigned'   => Delivery::where('rider_id', $rider->id)->where('status', 'assigned')->count(),
            'in_transit' => Delivery::where('rider_id', $rider->id)->whereIn('status', ['picked_up', 'out_for_delivery'])->count(),
            'delivered'  => Delivery::where('rider_id', $rider->id)->where('status', 'delivered')->whereDate('delivered_at', today())->count(),
        ];

        $activeDeliveries = Delivery::where('rider_id', $rider->id)
                                    ->whereNotIn('status', ['delivered', 'failed'])
                                    ->with(['order.user'])
                                    ->latest()
                                    ->get();

        return view('rider.dashboard', compact('stats', 'activeDeliveries', 'rider'));
    }

    public function deliveries()
    {
        $rider = $this->getRider();
        $deliveries = Delivery::where('rider_id', $rider->id)
                              ->with(['order.user'])
                              ->latest()
                              ->paginate(20);

        return view('rider.deliveries', compact('deliveries', 'rider'));
    }

    public function show(Delivery $delivery)
    {
        $rider = $this->getRider();
        abort_if($delivery->rider_id !== $rider->id, 403);
        $delivery->load(['order.user']);
        return view('rider.deliveries.show', compact('delivery', 'rider'));
    }

    public function updateStatus(Request $request, Delivery $delivery)
    {
        $rider = $this->getRider();
        abort_if($delivery->rider_id !== $rider->id, 403);

        $request->validate([
            'status' => 'required|in:picked_up,out_for_delivery,delivered',
            'proof_of_delivery' => 'required_if:status,delivered|image|max:5120', // 5MB max
        ]);

        try {
            DB::transaction(function () use ($request, $delivery, $rider) {
                $orderService = app(\App\Services\OrderService::class);
                
                $orderService->updateStatus($delivery->order, $request->status, 'rider');

                $update = ['status' => $request->status];
                
                if ($request->hasFile('proof_of_delivery')) {
                    $path = $request->file('proof_of_delivery')->store('proofs', 'public');
                    $update['proof_of_delivery'] = $path;
                }

                if ($request->status === 'picked_up') {
                    $update['picked_up_at'] = now();
                    // Notify Courier
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
                if ($request->status === 'delivered')        $update['delivered_at'] = now();

                $delivery->update($update);
            });

            return back()->with('success', 'Delivery status updated to ' . str_replace('_', ' ', $request->status) . '.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function toggleAvailability()
    {
        $rider = $this->getRider();
        $rider->update(['is_available' => !$rider->is_available]);
        $status = $rider->is_available ? 'Available' : 'Unavailable';
        return back()->with('success', 'You are now marked as ' . $status . '.');
    }
}
