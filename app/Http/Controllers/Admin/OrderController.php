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
        $riders = \App\Models\User::where('is_rider', true)->get();
        
        return view('admin.orders', compact('orders', 'statusFilter', 'riders'));
    }

    public function show($id)
    {
        $order = Order::with('user:id,name,email,phone')->findOrFail($id);
        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'items' => $order->items,
            'customer_info' => $order->customer_info,
            'courier_name' => $order->courier_name,
            'tracking_number' => $order->tracking_number,
            'created_at' => $order->created_at,
            'user' => $order->user ? [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
                'phone' => $order->user->phone,
            ] : null,
        ]);
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

    public function updateDelivery(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'rider_id' => 'required|exists:users,id',
            'delivery_type' => 'required|in:rider,courier',
            'courier_name' => 'nullable|string|max:100|required_if:delivery_type,courier',
            'tracking_number' => 'nullable|string|max:100|required_if:delivery_type,courier',
        ]);
        
        $order->update($data);
        return back()->with('success', 'Delivery information updated');
    }
}
