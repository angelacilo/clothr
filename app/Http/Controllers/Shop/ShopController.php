<?php

/**
 * FILE: Shop/ShopController.php
 * 
 * What this file does:
 * This controller handles the main "Shop" pages where customers can browse 
 * products. It includes searching, filtering by category, filtering by price, 
 * and sorting (like Price Low to High).
 * 
 * How it connects to the project:
 * - It is called by routes like "/shop" and "/category/{slug}" in routes/web.php.
 * - It uses the Product and Category models to fetch the inventory.
 * - The views it returns are in resources/views/shop/shop.blade.php and category.blade.php.
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Shows the main Shop page with all products.
     * 
     * This function handles filtering (Deals, Categories, Price Range)
     * and sorting (Newest, Price, Featured).
     * 
     * @param Request $request — contains all the filter/sort parameters
     * @return view — the shop page with products
     */
    public function index(Request $request)
    {
        // 1. Start a query for products that are NOT hidden/archived.
        $query = Product::where('isArchived', false);

        // 2. FILTER: Only show items that are "On Sale" if requested.
        if ($request->has('deals')) {
            $query->where('isOnSale', true);
        }

        // 3. FILTER: Only show products from a specific category.
        if ($request->has('category') && $request->category != 'all') {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // 4. FILTER: Price range checks.
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price < 5000) {
            $query->where('price', '<=', $request->max_price);
        }

        // 5. SORTING: Arrange products based on the user's choice.
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

        // 6. PAGINATION: Only load 24 products at a time to keep the page fast.
        $products = $query->paginate(24);
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.shop', compact('products', 'categories', 'sort'));
    }

    /**
     * Shows products for a specific category (e.g., just "Men's Apparel").
     * 
     * @param Request $request — contains sort/filter parameters
     * @param string $slug — the unique name of the category (e.g., "mens-apparel")
     * @return view — the category-specific shop page
     */
    public function category(Request $request, $slug)
    {
        // Find the category by its slug, or show a 404 if it doesn't exist.
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Start a query for products that belong to this specific category.
        $query = Product::where('category_id', $category->id)->where('isArchived', false);

        // Apply price filters.
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price < 5000) {
            $query->where('price', '<=', $request->max_price);
        }

        // Apply sorting.
        $sort = $request->get('sort', 'all');
        switch ($sort) {
            case 'price_low': $query->orderBy('price', 'asc'); break;
            case 'price_high': $query->orderBy('price', 'desc'); break;
            case 'newest': $query->orderBy('created_at', 'desc'); break;
            case 'featured': $query->where('isFeatured', true); break;
        }

        // Load 24 products at a time.
        $products = $query->paginate(24);
        $categories = Category::where('isVisible', true)->get();
        
        return view('shop.category', compact('category', 'products', 'categories', 'sort'));
    }
}
