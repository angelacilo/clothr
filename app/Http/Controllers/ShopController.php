<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;


class ShopController extends Controller
{
    public function index()
    {
        $featured = Product::where('isFeatured', true)->where('isArchived', false)->take(4)->get();
        $categories = Category::where('isVisible', true)->get();
        return view('shop.index', compact('featured', 'categories'));
    }

    public function shop(Request $request)
    {
        $query = Product::where('isArchived', false);

        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price < 5000) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('isFeatured', 'desc');
                break;
        }

        $products = $query->get();
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.shop', compact('products', 'categories', 'sort'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->where('isArchived', false)->get();
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.category', compact('category', 'products', 'categories'));
    }

    public function product($id)
    {
        $product = Product::with('category')->findOrFail($id);
        return view('shop.product', compact('product'));
    }

    public function cart()
    {
        return view('shop.cart');
    }

    public function checkout()
    {
        return view('shop.checkout');
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_info' => 'required|array',
            'items' => 'required|array',
            'total' => 'required|numeric',
        ]);

        $order = Order::create([
            'user_id' => auth()->id(),
            'customer_info' => $validated['customer_info'],
            'items' => $validated['items'],
            'total' => $validated['total'],
            'status' => 'Pending'
        ]);

        return response()->json(['success' => true, 'order_id' => $order->id]);
    }

    public function confirmation($id)
    {
        $order = Order::findOrFail($id);
        return view('shop.confirmation', compact('order'));
    }
}
