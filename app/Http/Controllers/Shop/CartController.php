<?php

/**
 * FILE: Shop/CartController.php
 * 
 * What this file does:
 * This controller manages the customer's shopping cart.
 * Since customers can browse without logging in, the cart is also saved 
 * in the browser's "localStorage" (JavaScript). This controller helps 
 * synchronizing (syncing) that local data with the database once the user logs in.
 * 
 * How it connects to the project:
 * - It is called by AJAX (background) requests from the cart.js file.
 * - It uses the CartItem and Product models.
 * - It returns JSON data so the cart updates instantly without refreshing the page.
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Shows the Cart Page.
     * 
     * @return view — the shopping cart page
     */
    public function index()
    {
        // 1. Get 5 random products to show as "You might also like" at the bottom.
        $recommendations = Product::where('isArchived', false)->inRandomOrder()->take(5)->get();
        return view('shop.cart', compact('recommendations'));
    }

    /**
     * Fetches all items in the logged-in user's cart.
     * 
     * @return json — list of cart items with product details
     */
    public function getCart()
    {
        // Get the items and include the product name, price, and images.
        $items = Auth::user()->cartItems()->with('product:id,name,price,images,isArchived')->get()->map(function($item) {
            return [
                'product_id' => $item->product_id,
                'size' => $item->size,
                'color' => $item->color,
                'quantity' => $item->quantity,
                'is_selected' => $item->is_selected,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'price' => $item->product->price,
                    'images' => $item->product->images,
                    'isArchived' => $item->product->isArchived,
                ] : null,
            ];
        });
        
        return response()->json($items);
    }

    /**
     * Synchronizes the browser's cart with the database.
     * 
     * This runs when a user logs in. It takes whatever was in their browser 
     * and saves it to the database so they don't lose their items.
     * 
     * @param Request $request — contains the list of items from localStorage
     * @return json — success message
     */
    public function sync(Request $request)
    {
        $items = $request->input('items', []);
        $user = Auth::user();

        // STEP 1: Clear out any old cart items in the database to prevent duplicates.
        CartItem::where('user_id', $user->id)->delete();
        
        // STEP 2: Loop through the browser items and save them to the database.
        foreach ($items as $item) {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $item['id'],
                'size' => $item['size'],
                'color' => $item['color'] ?? null,
                'quantity' => $item['quantity'],
                'is_selected' => $item['is_selected'] ?? true,
            ]);
        }

        return response()->json(['success' => true]);
    }
    
    /**
     * Updates the quantity or selection status of an item.
     * 
     * @param Request $request — contains product ID, size, color, and new quantity
     * @return json — success message
     */
    public function updateItem(Request $request) 
    {
        $user = Auth::user();
        
        // Find if the exact same variation (product + color + size) is already in the cart
        $item = CartItem::where([
            'user_id'    => $user->id,
            'product_id' => $request->id,
            'size'       => $request->size,
            'color'      => $request->color,
        ])->first();

        if ($item) {
            // If it exists, we ADD the new quantity to what was already there
            $item->quantity += $request->quantity;
            $item->is_selected = $request->is_selected ?? $item->is_selected;
            $item->save();
        } else {
            // If it's new, we CREATE a new row
            CartItem::create([
                'user_id'     => $user->id,
                'product_id'  => $request->id,
                'size'        => $request->size,
                'color'       => $request->color,
                'quantity'    => $request->quantity,
                'is_selected' => $request->is_selected ?? true,
            ]);
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Removes an item from the cart.
     * 
     * @param Request $request — contains the item details to delete
     * @return json — success message
     */
    public function removeItem(Request $request) 
    {
        // Delete the matching row from the cart_items table.
        Auth::user()->cartItems()
            ->where('product_id', $request->id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->delete();
            
        return response()->json(['success' => true]);
    }
}
