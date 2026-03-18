<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $recommendations = Product::where('isArchived', false)->inRandomOrder()->take(5)->get();
        return view('shop.cart', compact('recommendations'));
    }

    public function getCart()
    {
        $items = Auth::user()->cartItems()->with('product')->get();
        return response()->json($items);
    }

    public function sync(Request $request)
    {
        $items = $request->input('items', []);
        $user = Auth::user();

        // Let's do clear and replace for accurate sync
        CartItem::where('user_id', $user->id)->delete();
        
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
    
    public function updateItem(Request $request) 
    {
        $user = Auth::user();
        CartItem::updateOrCreate(
            [
                'user_id' => $user->id,
                'product_id' => $request->id,
                'size' => $request->size,
                'color' => $request->color,
            ],
            [
                'quantity' => $request->quantity,
                'is_selected' => $request->is_selected ?? true,
            ]
        );
        return response()->json(['success' => true]);
    }

    public function removeItem(Request $request) 
    {
        Auth::user()->cartItems()
            ->where('product_id', $request->id)
            ->where('size', $request->size)
            ->where('color', $request->color)
            ->delete();
        return response()->json(['success' => true]);
    }
}
