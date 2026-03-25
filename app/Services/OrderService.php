<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Place a new order using database-verified prices
     * NEVER trusts the total from the browser/request
     */
    public function placeOrder($userId, array $items, array $customerInfo)
    {
        return DB::transaction(function () use ($userId, $items, $customerInfo) {
            $calculatedTotal = 0;
            $processedItems = [];

            // Secure calculation: Load product prices from DB
            $productIds = collect($items)->pluck('id')->toArray();
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            foreach ($items as $item) {
                $productId = $item['id'];
                if (!$products->has($productId)) {
                    continue; // Skip invalid products
                }

                $product = $products->get($productId);
                $quantity = $item['quantity'] ?? 1;
                $price = $product->price;

                $calculatedTotal += ($price * $quantity);

                $processedItems[] = [
                    'id'       => $productId,
                    'name'     => $product->name,
                    'price'    => $price,
                    'quantity' => $quantity,
                    'size'     => $item['size'] ?? null,
                    'color'    => $item['color'] ?? null,
                    'category' => $product->category->name ?? 'N/A',
                ];
            }

            // Create the order
            $order = Order::create([
                'user_id'       => $userId,
                'customer_info' => $customerInfo,
                'items'         => $processedItems,
                'total'         => $calculatedTotal,
                'status'        => 'Pending'
            ]);

            // Clear only the checked-out items from the user's cart
            if (class_exists(\App\Models\CartItem::class)) {
                foreach ($items as $item) {
                    \App\Models\CartItem::where('user_id', $userId)
                        ->where('product_id', $item['id'])
                        ->where('size', $item['size'] ?? null)
                        ->where('color', $item['color'] ?? null)
                        ->delete();
                }
            }

            return $order;
        });
    }

    /**
     * Update order status with validation and timestamps
     */
    public function updateStatus(Order $order, string $newStatus)
    {
        $currentStatus = $order->status;

        // Valid transitions based on rules
        $validTransitions = [
            'Pending'    => ['Processing', 'Cancelled'],
            'Processing' => ['Pending', 'Shipped', 'Cancelled'],
            'Shipped'    => ['Processing', 'Delivered', 'Cancelled'],
            'Delivered'  => ['Shipped'], // allow rollback for mistakes
            'Cancelled'  => ['Pending'], // allow restoring
        ];

        if ($newStatus !== $currentStatus) {
            $allowed = $validTransitions[$currentStatus] ?? [];
            if (!in_array($newStatus, $allowed)) {
                throw new \Exception("Invalid state transition from {$currentStatus} to {$newStatus}.");
            }
        }

        $data = ['status' => $newStatus];

        // Auto-set timestamps based on status
        $now = now();
        switch ($newStatus) {
            case 'Processing':
                $data['processing_at'] = $now;
                break;
            case 'Shipped':
                if (!$order->processing_at) $data['processing_at'] = $now;
                $data['shipped_at'] = $now;
                break;
            case 'Delivered':
                if (!$order->processing_at) $data['processing_at'] = $now;
                if (!$order->shipped_at) $data['shipped_at'] = $now;
                $data['delivered_at'] = $now;
                break;
            case 'Cancelled':
                $data['cancelled_at'] = $now;
                break;
            case 'Pending':
                $data['processing_at'] = null;
                $data['shipped_at']    = null;
                $data['delivered_at']  = null;
                $data['cancelled_at']  = null;
                break;
        }

        $order->update($data);
        
        // --- CUSTOMER NOTIFICATION TRIGGER ---
        if ($order->user_id) {
            $displayId = 1000 + $order->id;
            $link = '/profile/order/' . $order->id;
            
            switch ($newStatus) {
                case 'Processing':
                    \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_processing', 
                        'Order is Being Processed', 
                        "Your order #{$displayId} is now being processed by our team. We will ship it out soon!", 
                        $link);
                    break;
                case 'Shipped':
                    $trackingText = $order->courier_tracking ? " Tracking: {$order->courier_tracking}" : "";
                    $courierText = $order->courier ? " Courier: {$order->courier}" : "";
                    \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_shipped', 
                        'Your Order Has Been Shipped', 
                        "Your order #{$displayId} is on its way!{$courierText}{$trackingText}", 
                        $link);
                    break;
                case 'Delivered':
                    \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_delivered', 
                        'Order Delivered Successfully', 
                        "Your order #{$displayId} has been delivered. Thank you for shopping with CLOTHR!", 
                        $link);
                    break;
                case 'Cancelled':
                    \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_cancelled', 
                        'Order Has Been Cancelled', 
                        "We are sorry. Your order #{$displayId} has been cancelled. Please contact us if you have any questions.", 
                        $link);
                    break;
            }
        }
        
        return $order;
    }
}
