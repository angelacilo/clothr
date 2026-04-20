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

            // Secure calculation: Load product prices from DB and lock for atomic stock update
            $productIds = collect($items)->pluck('id')->toArray();
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($items as $item) {
                $productId = $item['id'];
                if (!$products->has($productId)) {
                    continue; // Skip invalid products
                }

                $product = $products->get($productId);
                $quantity = $item['quantity'] ?? 1;
                $color = $item['color'] ?? null;
                $size  = $item['size'] ?? null;

                // --- STOCK VALIDATION & TOTAL DEDUCTION ---
                if ($product->stock < $quantity) {
                    throw new \Exception("Not enough stock for {$product->name}. Only {$product->stock} remaining.");
                }
                $product->stock -= $quantity;

                // --- VARIANT STOCK DEDUCTION ---
                if (!empty($product->variants)) {
                    $variants = $product->variants;
                    $variantUpdated = false;

                    foreach ($variants as &$v) {
                        if ($color === null || $v['color'] === $color) {
                            if ($size !== null && isset($v['sizes'][$size])) {
                                if ($v['sizes'][$size] < $quantity) {
                                    throw new \Exception("Not enough stock for {$product->name} variant ({$color} - {$size}).");
                                }
                                $v['sizes'][$size] -= $quantity;
                                $variantUpdated = true;
                                break;
                            }
                        }
                    }
                    if ($variantUpdated) {
                        $product->variants = $variants;
                    }
                }
                
                $product->save();

                $price = $product->price;

                $calculatedTotal += ($price * $quantity);

                $processedItems[] = [
                    'id'       => $productId,
                    'name'     => $product->name,
                    'price'    => $price,
                    'quantity' => $quantity,
                    'size'     => $size,
                    'color'    => $color,
                    'category' => $product->category->name ?? 'N/A',
                    'image'    => is_array($product->images) && count($product->images) > 0 ? $product->images[0] : null,
                ];
            }

            // Create the order
            $order = Order::create([
                'user_id'       => $userId,
                'customer_info' => $customerInfo,
                'items'         => $processedItems,
                'total'         => $calculatedTotal,
                'status'        => 'pending'
            ]);

            // Fire real-time event
            broadcast(new \App\Events\NewOrderPlaced($order))->toOthers();

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
    /**
     * Update order status with validation and timestamps
     */
    public function updateStatus(Order $order, string $newStatus, string $role = 'admin')
    {
        $newStatus = strtolower($newStatus);
        return DB::transaction(function () use ($order, $newStatus, $role) {
            $currentStatus = $order->status;

            // Valid transitions based on role permissions (The Plan)
            $validTransitions = [
                'pending'          => [
                    'admin' => ['processing', 'cancelled']
                ],
                'processing'       => [
                    'admin'   => ['pending', 'cancelled'],
                    'courier' => ['shipped']
                ],
                'shipped'          => [
                    'rider'   => ['picked_up'], // Rider marks as picked up from warehouse
                    'courier' => ['out_for_delivery'],
                    'admin'   => ['cancelled']
                ],
                'picked_up'        => [
                    'rider'   => ['out_for_delivery'],
                    'courier' => ['shipped'] // Can revert if mistake
                ],
                'out_for_delivery' => [
                    'rider'   => ['delivered'],
                    'courier' => ['shipped', 'lost']
                ],
                'delivered'        => [],
                'cancelled'        => [
                    'admin' => ['pending']
                ],
                'lost'             => [
                    'admin' => ['cancelled', 'pending']
                ],
            ];

            if ($newStatus !== $currentStatus) {
                $allowedForRole = $validTransitions[$currentStatus][$role] ?? [];
                
                // STRICTOR CHECK: Every role (including Admin) MUST stay within their assigned transitions
                if (!in_array($newStatus, $allowedForRole)) {
                    throw new \Exception("Unauthorized or invalid transition from {$currentStatus} to {$newStatus} for role: {$role}.");
                }
                
                $order->status = $newStatus;
            }

            // --- RESTORE STOCK IF CANCELLED (atomically) ---
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    $productId = $item['id'] ?? null;
                    if (!$productId) continue;

                    $product = Product::lockForUpdate()->find($productId);
                    if ($product) {
                        $qty = $item['quantity'] ?? 1;
                        $color = $item['color'] ?? null;
                        $size  = $item['size'] ?? null;

                        // Restore total stock
                        $product->stock += $qty;

                        // Restore variant stock
                        if (!empty($product->variants)) {
                            $variants = $product->variants;
                            $variantUpdated = false;

                            foreach ($variants as &$v) {
                                if ($color === null || $v['color'] === $color) {
                                    if ($size !== null && isset($v['sizes'][$size])) {
                                        $v['sizes'][$size] += $qty;
                                        $variantUpdated = true;
                                        break;
                                    }
                                }
                            }
                            if ($variantUpdated) {
                                $product->variants = $variants;
                            }
                        }
                        $product->save();
                    }
                }
            }

            // --- RE-DEDUCT STOCK IF UN-CANCELLED (Restore from Cancelled back to Pending) ---
            if ($currentStatus === 'cancelled' && $newStatus === 'pending') {
                foreach ($order->items as $item) {
                    $productId = $item['id'] ?? null;
                    if (!$productId) continue;

                    $product = Product::lockForUpdate()->find($productId);
                    if ($product) {
                        $qty = $item['quantity'] ?? 1;
                        $color = $item['color'] ?? null;
                        $size  = $item['size'] ?? null;

                        // Validate enough stock exists before restoring the order
                        if ($product->stock < $qty) {
                            throw new \Exception("Cannot restore order. Not enough stock for {$product->name}.");
                        }
                        $product->stock -= $qty;

                        // Deduct variant stock
                        if (!empty($product->variants)) {
                            $variants = $product->variants;
                            $variantUpdated = false;

                            foreach ($variants as &$v) {
                                if ($color === null || $v['color'] === $color) {
                                    if ($size !== null && isset($v['sizes'][$size])) {
                                        if ($v['sizes'][$size] < $qty) {
                                            throw new \Exception("Cannot restore order. Not enough stock for {$product->name} ({$color} - {$size}).");
                                        }
                                        $v['sizes'][$size] -= $qty;
                                        $variantUpdated = true;
                                        break;
                                    }
                                }
                            }
                            if ($variantUpdated) {
                                $product->variants = $variants;
                            }
                        }
                        $product->save();
                    }
                }
            }

            $data = ['status' => $newStatus];

            // Auto-set timestamps based on status
            $now = now();
            switch ($newStatus) {
                case 'processing':
                    $data['processing_at'] = $now;
                    break;
                case 'shipped':
                    if (!$order->processing_at) $data['processing_at'] = $now;
                    $data['shipped_at'] = $now;
                    break;
                case 'picked_up':
                    if (!$order->processing_at) $data['processing_at'] = $now;
                    if (!$order->shipped_at) $data['shipped_at'] = $now;
                    $data['picked_up_at'] = $now;
                    break;
                case 'out_for_delivery':
                    if (!$order->processing_at) $data['processing_at'] = $now;
                    if (!$order->shipped_at) $data['shipped_at'] = $now;
                    $data['out_for_delivery_at'] = $now;
                    break;
                case 'delivered':
                    if (!$order->processing_at) $data['processing_at'] = $now;
                    if (!$order->shipped_at) $data['shipped_at'] = $now;
                    $data['delivered_at'] = $now;
                    break;
                case 'cancelled':
                    $data['cancelled_at'] = $now;
                    break;
                case 'pending':
                    $data['processing_at'] = null;
                    $data['shipped_at']    = null;
                    $data['out_for_delivery_at'] = null;
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
                    case 'processing':
                        \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_processing', 
                            'Order is Being Processed', 
                            "Your order #{$displayId} is now being processed by our team. We will ship it out soon!", 
                            $link);
                        break;
                    case 'shipped':
                        $trackingText = $order->tracking_number ? " Tracking: {$order->tracking_number}" : "";
                        $courierText = $order->courier_name ? " Courier: {$order->courier_name}" : "";
                        \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_shipped', 
                            'Your Order Has Been Shipped', 
                            "Your order #{$displayId} is on its way!{$courierText}{$trackingText}", 
                            $link);
                        break;
                    case 'out_for_delivery':
                        \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_out_for_delivery', 
                            'Your Order is Out for Delivery', 
                            "Your order #{$displayId} is out for delivery with our rider. Please be ready to receive it!", 
                            $link);
                        break;
                    case 'delivered':
                        \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_delivered', 
                            'Order Delivered Successfully', 
                            "Your order #{$displayId} has been delivered. Thank you for shopping with CLOTHR!", 
                            $link);
                        break;
                    case 'cancelled':
                        \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_cancelled', 
                            'Order Has Been Cancelled', 
                            "We are sorry. Your order #{$displayId} has been cancelled. Please contact us if you have any questions.", 
                            $link);
                        break;
                }
            }

            // --- REAL-TIME BROADCAST ---
            broadcast(new \App\Events\OrderStatusUpdated($order))->toOthers();
            
            return $order;
        });
    }
}
