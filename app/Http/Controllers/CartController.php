<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Display shopping cart
     */
    public function index()
    {
        $user = auth()->user();
        $cart = Cart::with('items.product')
                    ->where('user_id', $user->user_id)
                    ->firstOrCreate(['user_id' => $user->user_id]);

        $cartItems = $cart->items;
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * ($item->product->sale_price ?? $item->product->price);
        });

        $shippingCost = 0;
        $tax = $subtotal * 0.10; // 10% tax
        $total = $subtotal + $shippingCost + $tax;

        return view('cart.index', compact('cart', 'cartItems', 'subtotal', 'tax', 'shippingCost', 'total'));
    }

    /**
     * Add item to cart via AJAX
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $user = auth()->user();

        // Get or create cart
        $cart = Cart::where('user_id', $user->user_id)
                   ->firstOrCreate(['user_id' => $user->user_id]);

        // Check if item already exists
        $cartItem = CartItem::where('cart_id', $cart->cart_id)
                           ->where('product_id', $product->product_id)
                           ->first();

        if ($cartItem) {
            // Update quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'cart_id' => $cart->cart_id,
                'product_id' => $product->product_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart!',
            'cartCount' => $cart->items()->count()
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::findOrFail($itemId);
        
        // Verify ownership
        if ($cartItem->cart->user_id !== auth()->user()->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart updated!'
        ]);
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId)
    {
        $cartItem = CartItem::findOrFail($itemId);
        
        // Verify ownership
        if ($cartItem->cart->user_id !== auth()->user()->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart!'
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clear()
    {
        $user = auth()->user();
        CartItem::whereIn('cart_id', 
            Cart::where('user_id', $user->user_id)->pluck('cart_id')
        )->delete();

        return back()->with('success', 'Cart cleared!');
    }

    /**
     * Get cart count via AJAX
     */
    public function getCount()
    {
        $count = CartItem::whereIn('cart_id',
            Cart::where('user_id', auth()->user()->user_id)->pluck('cart_id')
        )->sum('quantity');

        return response()->json(['count' => $count]);
    }
}
