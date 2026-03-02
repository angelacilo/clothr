<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display checkout page
     */
    public function checkout()
    {
        $user = auth()->user();
        $cart = Cart::with('items.product')
                    ->where('user_id', $user->user_id)
                    ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Calculate totals
        $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * ($item->product->sale_price ?? $item->product->price);
        });
        
        $shippingCost = $subtotal >= 50 ? 0 : 5; // Free shipping on $50+
        $tax = $subtotal * 0.10; // 10% tax
        $total = $subtotal + $shippingCost + $tax;

        return view('checkout.index', compact('cart', 'subtotal', 'shippingCost', 'tax', 'total'));
    }

    /**
     * Place order
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:credit_card,paypal,bank_transfer,cash_on_delivery',
        ]);

        $user = auth()->user();
        $cart = Cart::with('items.product')
                    ->where('user_id', $user->user_id)
                    ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return back()->with('error', 'Your cart is empty!');
        }

        try {
            DB::beginTransaction();

            // Calculate totals
            $subtotal = $cart->items->sum(function ($item) {
                return $item->quantity * ($item->product->sale_price ?? $item->product->price);
            });
            
            $shippingCost = $subtotal >= 50 ? 0 : 5;
            $tax = $subtotal * 0.10;
            $total = $subtotal + $shippingCost + $tax;

            // Create order
            $order = Order::create([
                'user_id' => $user->user_id,
                'total_amount' => $total,
                'order_status' => 'pending',
                'order_date' => now(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'shipping_address' => $request->shipping_address,
            ]);

            // Create order items and update inventory
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item->product->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->sale_price ?? $item->product->price,
                ]);

                // Update inventory
                $inventory = Inventory::where('product_id', $item->product->product_id)->first();
                if ($inventory) {
                    $inventory->available_qty -= $item->quantity;
                    $inventory->sold_qty += $item->quantity;
                    $inventory->save();
                }
            }

            // Create payment record (starting as pending)
            \App\Models\Payment::create([
                'order_id' => $order->order_id,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'amount' => $total,
            ]);

            // Clear cart
            CartItem::where('cart_id', $cart->cart_id)->delete();

            DB::commit();

            return redirect()->route('order.confirmation', $order->order_id)
                           ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    /**
     * Order confirmation page
     */
    public function confirmation($orderId)
    {
        $order = Order::with(['items.product', 'payment'])->findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== auth()->user()->user_id) {
            abort(403, 'Unauthorized');
        }

        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Show user's order history
     */
    public function history()
    {
        $orders = Order::where('user_id', auth()->user()->user_id)
                       ->with(['items.product', 'payment'])
                       ->orderByDesc('created_at')
                       ->paginate(10);

        return view('orders.history', compact('orders'));
    }

    /**
     * Show order details
     */
    public function show($orderId)
    {
        $order = Order::with(['items.product', 'payment', 'delivery'])->findOrFail($orderId);

        // Verify ownership
        if ($order->user_id !== auth()->user()->user_id) {
            abort(403, 'Unauthorized');
        }

        return view('orders.show', compact('order'));
    }
}
