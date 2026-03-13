<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Home page: new arrivals and categories (real data from DB)
     */
    public function home()
    {
        $newArrivals = Product::with(['category', 'inventory', 'images'])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();
        $categories = Category::withCount('products')->get();
        return view('home', compact('newArrivals', 'categories'));
    }

    /**
     * Display all products with optional filtering (active only for customers)
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'images', 'reviews'])
            ->where('status', 'active');

        // Filter by category
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        // Search by name
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Sort options
        switch ($request->get('sort', 'newest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderByDesc('order_items_count');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
        }

        $products = $query->paginate(12);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Display a single product with reviews
     */
    public function show($id)
    {
        $product = Product::with(['category', 'inventory', 'images', 'reviews.user', 'variants'])
            ->where('status', 'active')
            ->findOrFail($id);

        // Increment view count if needed (optional)
        // $product->increment('views');

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('product_id', '!=', $id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Get product details via AJAX
     */
    public function getDetails($id)
    {
        $product = Product::with(['inventory', 'images'])->findOrFail($id);

        return response()->json([
            'product_id' => $product->product_id,
            'name' => $product->name,
            'price' => $product->price,
            'sale_price' => $product->sale_price,
            'description' => $product->description,
            'stock' => $product->inventory ? $product->inventory->available_qty : 0,
            'images' => $product->images->map(function ($img) {
                return [
                    'id' => $img->product_image_id,
                    'url' => asset('storage/' . $img->image_path)
                ];
            })->toArray()
        ]);
    }
}
