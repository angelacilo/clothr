<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function getCart()
    {
        $items = Auth::user()->cartItems()->with('product')->get();
        return response()->json($items);
    }

    public function sync(Request $request)
    {
        $items = $request->input('items', []);
        $user = Auth::user();

        // Clear existing cart and replace with new one (simplest sync)
        // Or merge logic. Let's do merge based on product_id and size.
        
        foreach ($items as $item) {
            CartItem::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'product_id' => $item['id'],
                    'size' => $item['size'],
                ],
                [
                    'quantity' => $item['quantity'],
                    'is_selected' => $item['is_selected'] ?? true,
                ]
            );
        }

        return response()->json(['success' => true]);
    }
    
    public function updateItem(Request $request) {
        $user = Auth::user();
        CartItem::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->id,
                'size' => $request->size,
            ],
            [
                'quantity' => $request->quantity,
                'is_selected' => $request->is_selected ?? true,
            ]
        );
        return response()->json(['success' => true]);
    }

    public function removeItem(Request $request) {
        Auth::user()->cartItems()
            ->where('product_id', $request->id)
            ->where('size', $request->size)
            ->delete();
        return response()->json(['success' => true]);
    }
}
