<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * HOW STOCK WORKS IN THIS PROJECT:
     * - The `variants` JSON column is the MASTER source of truth for stock if the product has variants.
     * - `products.stock` is ALWAYS auto-calculated from the sum of variant sizes.
     * - When an order is placed, stock is deducted from the specific variant size.
     * - When an order is cancelled, stock is restored to the specific variant size.
     * - `DB::transaction()` and `lockForUpdate()` are ALWAYS used to prevent race conditions.
     */

    public function placeOrder($userId, array $items, array $customerInfo)
    {
        return DB::transaction(function () use ($userId, $items, $customerInfo) {
            $productIds = collect($items)->pluck('id')->toArray();
            
            // Step 1: Load all products with lockForUpdate
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $calculatedTotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $productId = $item['id'];
                if (!$products->has($productId)) {
                    continue; // Skip invalid products
                }

                $product = $products->get($productId);
                
                // Step 2: Validate all items (stock check)
                $this->validateStock($product, $item);

                // Step 3: Deduct stock for all items
                $this->deductStock($product, $item);

                $price = $product->price;
                $quantity = $item['quantity'] ?? 1;
                $calculatedTotal += ($price * $quantity);

                // Step 4: Build processedItems array
                $processedItems[] = [
                    'id'       => $productId,
                    'name'     => $product->name,
                    'price'    => $price,
                    'quantity' => $quantity,
                    'size'     => $item['size'] ?? null,
                    'color'    => $item['color'] ?? null,
                    'category' => $product->category->name ?? 'N/A',
                    'image'    => is_array($product->images) && count($product->images) > 0 ? $product->images[0] : null,
                ];
            }

            // Step 5: Create order
            $order = Order::create([
                'user_id'       => $userId,
                'customer_info' => $customerInfo,
                'items'         => $processedItems,
                'total'         => $calculatedTotal,
                'status'        => 'pending'
            ]);

            // Step 6: Clear cart items
            if (class_exists(\App\Models\CartItem::class)) {
                foreach ($items as $item) {
                    \App\Models\CartItem::where('user_id', $userId)
                        ->where('product_id', $item['id'])
                        ->where('size', $item['size'] ?? null)
                        ->where('color', $item['color'] ?? null)
                        ->delete();
                }
            }

            // Step 7: Fire events
            broadcast(new \App\Events\NewOrderPlaced($order))->toOthers();

            return $order;
        });
    }

    private function validateStock($product, $item): void
    {
        $quantity = $item['quantity'] ?? 1;
        $color = $item['color'] ?? null;
        $size  = $item['size'] ?? null;

        if (!empty($product->variants)) {
            $hasSufficientStock = false;
            foreach ($product->variants as $v) {
                if ($color === null || $v['color'] === $color) {
                    if ($size !== null && isset($v['sizes'][$size])) {
                        if ($v['sizes'][$size] >= $quantity) {
                            $hasSufficientStock = true;
                        } else {
                            throw new \Exception("Only {$v['sizes'][$size]} left in {$color} {$size} for {$product->name}.");
                        }
                        break;
                    }
                }
            }
            if (!$hasSufficientStock) {
                throw new \Exception("The requested variant for {$product->name} is not available.");
            }
        } else {
            if ($product->stock < $quantity) {
                throw new \Exception("Only {$product->stock} items left for {$product->name}.");
            }
        }
    }

    private function deductStock($product, $item): void
    {
        $quantity = $item['quantity'] ?? 1;
        $color = $item['color'] ?? null;
        $size  = $item['size'] ?? null;

        if (!empty($product->variants)) {
            $variants = $product->variants;
            $variantUpdated = false;

            foreach ($variants as &$v) {
                if ($color === null || $v['color'] === $color) {
                    if ($size !== null && isset($v['sizes'][$size])) {
                        $v['sizes'][$size] -= $quantity;
                        $variantUpdated = true;
                        break;
                    }
                }
            }
            if ($variantUpdated) {
                $product->variants = $variants;
                // Recalculate products.stock
                $totalStock = 0;
                foreach ($product->variants as $v) {
                    foreach ($v['sizes'] ?? [] as $qty) {
                        $totalStock += $qty;
                    }
                }
                $product->stock = $totalStock;
            }
        } else {
            $product->stock -= $quantity;
        }
        $product->save();
    }

    private function restoreStock($product, $item): void
    {
        $quantity = $item['quantity'] ?? 1;
        $color = $item['color'] ?? null;
        $size  = $item['size'] ?? null;

        if (!empty($product->variants)) {
            $variants = $product->variants;
            $variantUpdated = false;

            foreach ($variants as &$v) {
                if ($color === null || $v['color'] === $color) {
                    if ($size !== null && isset($v['sizes'][$size])) {
                        $v['sizes'][$size] += $quantity;
                        $variantUpdated = true;
                        break;
                    }
                }
            }
            if ($variantUpdated) {
                $product->variants = $variants;
                // Recalculate products.stock
                $totalStock = 0;
                foreach ($product->variants as $v) {
                    foreach ($v['sizes'] ?? [] as $qty) {
                        $totalStock += $qty;
                    }
                }
                $product->stock = $totalStock;
            }
        } else {
            $product->stock += $quantity;
        }
        $product->save();
    }

    public function updateStatus(Order $order, string $newStatus, string $role = 'admin')
    {
        $newStatus = strtolower($newStatus);
        return DB::transaction(function () use ($order, $newStatus, $role) {
            $currentStatus = $order->status;

            // Valid transitions based on role permissions
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
                
                // STRICTER CHECK: Every role (including Admin) MUST stay within their assigned transitions
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
                        $this->restoreStock($product, $item);
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
                        $this->validateStock($product, $item);
                        $this->deductStock($product, $item);
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
