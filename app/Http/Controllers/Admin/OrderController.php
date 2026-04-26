<?php

/**
 * FILE: Admin/OrderController.php
 * 
 * What this file does:
 * This controller manages the list of customer orders in the admin panel.
 * It allows the admin to view all orders, search for specific orders, 
 * change the order status (like marking it as Shipped), and update 
 * courier/tracking information.
 * 
 * How it connects to the project:
 * - It is called by routes in routes/admin.php.
 * - It uses the OrderService to handle the logic of updating statuses 
 *   (which also handles stock restoration if cancelled).
 * - It uses the Order and Courier models to talk to the database.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Courier;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrderController extends Controller
{
    // Holds the OrderService which contains the business logic for orders.
    protected $orderService;

    // The constructor runs automatically and injects the OrderService.
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Shows the list of all orders.
     * 
     * This function runs when the admin visits /admin/orders.
     * It allows filtering by status and searching by ID or customer name.
     * 
     * @param Request $request — contains filter and search inputs
     * @return view — the orders list page
     */
    public function index(Request $request)
    {
        // Start a query to get all orders, newest first.
        // "with('user')" loads the customer info efficiently.
        $query = Order::with('user')->orderBy('created_at', 'desc');

        // Check if the admin selected a specific status filter (e.g., "Pending").
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Check if the admin typed something in the search bar.
        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                // If it looks like an order ID (e.g., "#1005"), clean it up.
                $cleanId = str_replace('#', '', $search);
                if (is_numeric($cleanId)) {
                    // Our display IDs start at 1001, so we subtract 1000 to find the DB ID.
                    $rawId = (int)$cleanId > 1000 ? (int)$cleanId - 1000 : (int)$cleanId;
                    $q->where('id', $rawId);
                } else {
                    // Otherwise, search by customer info or tracking number.
                    $q->where('customer_info', 'like', "%{$search}%")
                      ->orWhere('tracking_number', 'like', "%{$search}%");
                }
            });
        }

        // Get the results, 20 per page.
        $orders = $query->paginate(20);
        // Get all available couriers for the "Update Courier" dropdown.
        $couriers = Courier::orderBy('name')->get();
        
        return view('admin.orders', compact('orders', 'statusFilter', 'couriers'));
    }

    /**
     * Fetches details of a single order for the popup modal.
     * 
     * This is called via JavaScript (AJAX) when the admin clicks "View Details".
     * 
     * @param int $id — the ID of the order
     * @return json — order details in data format
     */
    public function show($id)
    {
        // Find the order or error out if not found.
        $order = Order::with('user:id,name,email,phone')->findOrFail($id);
        
        // Loop through the items in the order to make sure they have images.
        $items = collect($order->items)->map(function($item) {
            // Default placeholder if no image exists.
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

        // Send back a clean JSON response.
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

    /**
     * Updates the status of an order (e.g., Pending -> Shipped).
     * 
     * @param Request $request — contains the new status
     * @param int $id — the ID of the order
     * @return redirect — back to the previous page with success/error message
     */
    public function updateStatus(Request $request, $id)
    {
        // Ensure the status provided is one of the allowed options.
        $request->validate(['status' => 'required|in:pending,processing,shipped,out_for_delivery,delivered,cancelled,lost']);
        
        $order = Order::findOrFail($id);

        try {
            // Use the Service to handle the transition. 
            // The service checks if the transition is allowed for the 'admin' role.
            $this->orderService->updateStatus($order, $request->status, 'admin');
            return back()->with('success', 'Order status updated to ' . $request->status);
        } catch (\Exception $e) {
            // If the transition is blocked (e.g., Delivered -> Pending), show the error.
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Updates the courier name and tracking number.
     * 
     * @param Request $request — contains courier details
     * @param int $id — the ID of the order
     * @return redirect — back to the previous page
     */
    public function updateCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Validation check for the input.
        $data = $request->validate([
            'courier_name'    => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);
        
        // Automatically find the courier's code (like "JT" for J&T Express).
        if (!empty($data['courier_name'])) {
            $courier = \App\Models\Courier::where('name', $data['courier_name'])->first();
            if ($courier) {
                $data['courier_service'] = $courier->code;
            }
        } else {
            $data['courier_service'] = null;
        }

        // Save the updated info to the database.
        $order->update($data);
        return back()->with('success', 'Courier information updated');
    }
}
