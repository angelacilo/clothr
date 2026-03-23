<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('isArchived', false);

        if ($request->has('deals')) {
            $query->where('isOnSale', true);
        }

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

        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'featured':
                $query->where('isFeatured', true)->orderBy('created_at', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(24);
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.shop', compact('products', 'categories', 'sort'));
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $query = Product::where('category_id', $category->id)->where('isArchived', false);

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price') && $request->max_price < 5000) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'all');
        switch ($sort) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'featured': $query->where('isFeatured', true); break;
        }

        $products = $query->paginate(24);
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.category', compact('category', 'products', 'categories', 'sort'));
    }
}
