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
        $order = Order::with('user:id,name,email,phone')->findOrFail($id);
        $items = collect($order->items)->map(function($item) {
            $svgPlaceholder = "data:image/svg+xml;charset=UTF-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='64' height='64' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' fill='%23f1f5f9'/%3E%3Cpath d='M22 26c0-2.2 1.8-4 4-4s4 1.8 4 4-1.8 4-4 4-4-1.8-4-4zm18 12H24c-1.8 0-3.3-1.2-3.8-2.9L24 30l6.5 8.5 5.5-7.5 7.8 11.2c-.8 1.1-2.1 1.8-3.8 1.8z' fill='%23cbd5e1'/%3E%3C/svg%3E";
            if (empty($item['image'])) {
                $product = \App\Models\Product::find($item['id']);
                if ($product && !empty($product->images)) {
                    $item['image'] = is_array($product->images) ? $product->images[0] : $product->images;
                } else {
                    $item['image'] = $svgPlaceholder;
                }
            }
            return $item;
        })->toArray();

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'items' => $items,
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

    public function updateCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'courier_name'    => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);
        
        // Find corresponding courier code for the portal logic
        if (!empty($data['courier_name'])) {
            $courier = \App\Models\Courier::where('name', $data['courier_name'])->first();
            if ($courier) {
                $data['courier_service'] = $courier->code;
            }
        } else {
            $data['courier_service'] = null;
        }

        $order->update($data);
        return back()->with('success', 'Courier information updated');
    }
}
