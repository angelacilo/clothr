<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;

class DashboardController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $query = auth()->user()->riderOrders()->with('user')->latest();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $orders = $query->paginate(15);
        return view('rider.dashboard', compact('orders', 'status'));
    }

    public function updateStatus(Request $request, $id)
    {
        $order = auth()->user()->riderOrders()->findOrFail($id);
        
        $request->validate([
            'status' => 'required|string'
        ]);

        try {
            $this->orderService->updateStatus($order, $request->status);
            return back()->with('success', 'Order status updated to ' . $request->status);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
