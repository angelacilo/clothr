<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment', 'delivery']);

        // Status filter
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Search filter
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . request('search') . '%');
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'delivery', 'address']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the status of an order (AJAX).
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['order_status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully!',
            'status' => $request->status,
        ]);
    }

    /**
     * Edit the order (if needed).
     */
    public function edit(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'delivery']);
        return view('admin.orders.edit', compact('order'));
    }

    /**
     * Update the order.
     */
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'tracking_num' => 'nullable|string',
        ]);

        $order->update($validated);

        return redirect()->route('admin.orders.show', $order->order_id)
            ->with('success', 'Order updated successfully!');
    }

    /**
     * Remove the order from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted successfully!');
    }

    // --- API Methods ---------------------------------------------------------

    /** GET /api/admin/orders */
    public function apiIndex(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment']);

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($query->latest()->paginate(15));
    }

    /** GET /api/admin/orders/{order} */
    public function apiShow(Order $order)
    {
        $order->load(['user', 'items.product.images', 'payment']);
        return response()->json($order);
    }

    /** PUT /api/admin/orders/{order} */
    public function apiUpdate(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['order_status' => $request->status]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
        ]);
    }
}
