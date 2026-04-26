<?php

/**
 * FILE: OrderService.php
 * WHAT IT DOES: This is the "Engine" for handling customer orders and inventory security.
 * WHY: It makes sure that when someone buys a shirt, the stock goes DOWN, and if they cancel, the stock goes back UP.
 * HOW IT WORKS: It uses "Database Transactions" (DB::transaction) to ensure that if something goes wrong during checkout, the whole process is cancelled and no data is corrupted.
 */

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * HOW STOCK WORKS (For the Panelist):
     * 1. The MASTER source of truth for stock is the "variants" column in the products table.
     * 2. When an order is placed, we find the specific COLOR and SIZE the customer chose and subtract from its quantity.
     * 3. We use "lockForUpdate()" to prevent two people from buying the very last item at the exact same time (Race Condition).
     */

    /**
     * WHAT IT DOES: Handles the entire "Place Order" process.
     * STEP 1: Locks the products so nobody else can change them.
     * STEP 2: Checks if there is enough stock for every item.
     * STEP 3: Subtracts the stock from the database.
     * STEP 4: Creates the Order record.
     * STEP 5: Clears the customer's shopping cart.
     */
    public function placeOrder($userId, array $items, array $customerInfo)
    {
        return DB::transaction(function () use ($userId, $items, $customerInfo) {
            $productIds = collect($items)->pluck('id')->toArray();
            
            // SECURITY: Lock the product rows so stock calculations are 100% accurate.
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $calculatedTotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $productId = $item['id'];
                if (!$products->has($productId)) {
                    continue; 
                }

                $product = $products->get($productId);
                
                // Check if we have enough units left.
                $this->validateStock($product, $item);

                // Subtract the units from the inventory.
                $this->deductStock($product, $item);

                $price = $product->price;
                $quantity = $item['quantity'] ?? 1;
                $calculatedTotal += ($price * $quantity);

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

            // Create the record in the "orders" table.
            $order = Order::create([
                'user_id'       => $userId,
                'customer_info' => $customerInfo,
                'items'         => $processedItems,
                'total'         => $calculatedTotal,
                'status'        => 'pending'
            ]);

            // Automatically remove these items from the cart.
            if (class_exists(\App\Models\CartItem::class)) {
                foreach ($items as $item) {
                    \App\Models\CartItem::where('user_id', $userId)
                        ->where('product_id', $item['id'])
                        ->where('size', $item['size'] ?? null)
                        ->where('color', $item['color'] ?? null)
                        ->delete();
                }
            }

            // BROADCAST: Send a real-time notification to the Admin that a new order arrived.
            broadcast(new \App\Events\NewOrderPlaced($order))->toOthers();

            return $order;
        });
    }

    /**
     * WHAT IT DOES: Checks if the requested color/size has enough stock.
     */
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

    /**
     * WHAT IT DOES: Reduces the stock quantity in the database.
     */
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
                
                // Recalculate the main "stock" column to keep it in sync with the variations.
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

    /**
     * WHAT IT DOES: Adds stock back to the database (used when an order is cancelled).
     */
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

    /**
     * WHAT IT DOES: Updates the status of an order (e.g., from "Pending" to "Shipped").
     * HOW: It checks if the "Role" (Admin, Courier, Rider) is allowed to make that change.
     */
    public function updateStatus(Order $order, string $newStatus, string $role = 'admin')
    {
        $newStatus = strtolower($newStatus);
        return DB::transaction(function () use ($order, $newStatus, $role) {
            $currentStatus = $order->status;

            // STATUS TRANSITION MAP: 
            // We only allow specific status changes to keep the process professional.
            $validTransitions = [
                'pending'          => [
                    'admin' => ['processing', 'cancelled']
                ],
                'processing'       => [
                    'admin'   => ['pending', 'cancelled'],
                    'courier' => ['shipped']
                ],
                'shipped'          => [
                    'rider'   => ['picked_up'], 
                    'courier' => ['out_for_delivery'],
                    'admin'   => ['cancelled']
                ],
                'picked_up'        => [
                    'rider'   => ['out_for_delivery'],
                    'courier' => ['shipped'] 
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
                
                // SECURITY: Verify that the person clicking the button has permission.
                if (!in_array($newStatus, $allowedForRole)) {
                    throw new \Exception("Unauthorized or invalid transition from {$currentStatus} to {$newStatus} for role: {$role}.");
                }
                
                $order->status = $newStatus;
            }

            // AUTO-RESTORE: If the admin cancels an order, the stock is automatically returned to the shelves.
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

            // UN-CANCEL: If a cancelled order is brought back to life, we deduct the stock again.
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

            // LOGGING: Save the exact time each status changed (e.g., "shipped_at").
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
            
            // NOTIFICATIONS: Send a message to the customer's phone/dashboard about their order status.
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
                        \App\Models\UserNotification::notify($order->user_id, $order->id, 'order_shipped', 
                            'Your Order Has Been Shipped', 
                            "Your order #{$displayId} is on its way!", 
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

            // REAL-TIME: Automatically update the customer's screen without them having to refresh.
            broadcast(new \App\Events\OrderStatusUpdated($order))->toOthers();
            
            return $order;
        });
    }
}
