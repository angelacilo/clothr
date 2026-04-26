<?php

/**
 * FILE: Shop/OrderController.php
 * 
 * What this file does:
 * This controller handles the "Checkout" process where customers finalize their purchase.
 * It takes the items from the cart, the customer's shipping address, and 
 * uses the OrderService to create a official order record in the database.
 * 
 * How it connects to the project:
 * - It is called when the customer clicks "Place Order" on the checkout page.
 * - It uses the OrderService to handle inventory security and stock deduction.
 * - It uses the Order and Address models.
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Models\Order;

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
     * Shows the Checkout Page.
     * 
     * @return view — the checkout page with the user's saved addresses
     */
    public function checkout()
    {
        // Get all saved addresses for the logged-in user so they can pick one.
        $addresses = auth()->user()->addresses ?? [];
        return view('shop.checkout', compact('addresses'));
    }

    /**
     * Processes the order and saves it to the database.
     * 
     * @param Request $request — contains customer info and cart items
     * @return json — order success/error response
     */
    public function placeOrder(Request $request)
    {
        // VALIDATION: Ensure the data sent from the browser is valid.
        $validated = $request->validate([
            'customer_info' => 'required|array',
            'items' => 'required|array',
        ]);

        $customer_info = $validated['customer_info'];
        
        /**
         * SECURITY: Prevent duplicate orders.
         * We check if the same user placed a "pending" order in the last 15 seconds.
         * This prevents people from accidentally placing the same order twice 
         * if they double-click the "Place Order" button.
         */
        $recentOrder = Order::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subSeconds(15))
            ->first();

        if ($recentOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Your order was already placed successfully. Please check your Orders page.'
            ], 429); // 429 = Too Many Requests
        }

        // If the customer chose a saved address, load those details from the database.
        if (!empty($customer_info['address_id'])) {
            // SECURITY: Ensure the address ID actually belongs to the logged-in user.
            $address = \App\Models\Address::where('id', $customer_info['address_id'])
                                        ->where('user_id', auth()->id())
                                        ->firstOrFail();
                                        
            $customer_info = [
                'first_name'     => $address->first_name,
                'last_name'      => $address->last_name,
                'email'          => auth()->user()->email,
                'phone'          => $address->phone,
                'address_line_1' => $address->address_line_1,
                'city'           => $address->city,
                'zip_code'       => $address->zip_code,
                'country'        => $address->country,
                'region'         => $address->region ?? '',
            ];
        }

        try {
            // STEP 1: Use the OrderService to create the order.
            // The service handles calculating the final total and subtracting stock.
            $order = $this->orderService->placeOrder(auth()->id(), $validated['items'], $customer_info);
            
            // STEP 2: Create an Admin Notification so the store owner knows a new order arrived.
            \App\Models\Notification::createNotification(
                'new_order',
                'New Order Placed',
                'Order #' . (1000 + $order->id) . ' was placed by ' . ($customer_info['first_name'] ?? 'a customer'),
                '/admin/orders'
            );

            // Return success and the new order ID.
            return response()->json(['success' => true, 'order_id' => $order->id]);
            
        } catch (\Exception $e) {
            // If anything went wrong (like out of stock), return the error message.
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Shows the Order Confirmation page after a successful purchase.
     * 
     * @param int $id — the ID of the order
     * @return view — the thank you / confirmation page
     */
    public function confirmation($id)
    {
        // SECURITY: Ensure the order belongs to the person trying to view it.
        $order = Order::where('user_id', auth()->id())->findOrFail($id);
        
        return view('shop.confirmation', compact('order'));
    }
}
