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
            'status' => 'required|in:out_for_delivery,delivered',
        ]);

        try {
            DB::transaction(function () use ($request, $delivery) {
                $orderService = app(\App\Services\OrderService::class);
                
                // Map delivery status to order status if different
                // In our plan: out_for_delivery (delivery) = out_for_delivery (order)
                // delivered (delivery) = delivered (order)
                
                $orderService->updateStatus($delivery->order, $request->status, 'rider');

                $update = ['status' => $request->status];
                if ($request->status === 'out_for_delivery') $update['picked_up_at'] = now();
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
