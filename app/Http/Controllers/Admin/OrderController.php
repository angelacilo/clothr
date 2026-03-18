<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        $statusFilter = $request->get('status', 'all');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $cleanId = str_replace('#', '', $search);
                if (is_numeric($cleanId)) {
                    $rawId = (int)$cleanId > 1000 ? (int)$cleanId - 1000 : (int)$cleanId;
                    $q->where('id', $rawId);
                } else {
                    $q->where('customer_info', 'like', "%{$search}%")
                      ->orWhere('tracking_number', 'like', "%{$search}%");
                }
            });
        }

        $orders = $query->paginate(20);
        return view('admin.orders', compact('orders', 'statusFilter'));
    }

    public function show($id)
    {
        $order = Order::with('user')->findOrFail($id);
        return response()->json($order);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:Pending,Processing,Shipped,Delivered,Cancelled']);
        
        $order = Order::findOrFail($id);

        try {
            $this->orderService->updateStatus($order, $request->status);
            return back()->with('success', 'Order status updated to ' . $request->status);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'courier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);
        
        $order->update($data);
        return back()->with('success', 'Courier information updated');
    }
}
