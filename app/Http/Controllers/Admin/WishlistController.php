<?php

/**
 * FILE: Admin/WishlistController.php
 * 
 * What this file does:
 * This controller allows the admin to see which products customers are 
 * adding to their wishlists. This is valuable market data that helps 
 * the admin know which items are trending.
 * 
 * How it connects to the project:
 * - It is called by the route "admin.wishlists" in routes/admin.php.
 * - It uses the Wishlist model to fetch data.
 * - The view it returns is resources/views/admin/wishlists.blade.php.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    /**
     * Shows the wishlist analytics page.
     * 
     * This function lists every item currently in a customer's wishlist.
     * It also calculates a "Top 10" list of the most wishlisted products.
     * 
     * @param Request $request — contains search and category filters
     * @return view — the wishlists page
     */
    public function wishlistIndex(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        // Start a query to get wishlist items with related user and product info.
        $query = Wishlist::with([
            'user:id,name,email',
            'product:id,name,price,images,category_id',
            'product.category:id,name'
        ]);

        // Filter by user name/email or product name if searched.
        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Filter by product category.
        if ($categoryId) {
            $query->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Get the paginated results.
        $wishlists = $query->latest()->paginate(20)->withQueryString();

        // ANALYTICS: Find the Top 10 most wishlisted products.
        // We count how many times each product_id appears in the wishlists table.
        $topProducts = Wishlist::select('product_id')
            ->selectRaw('count(*) as total') // Count the occurrences.
            ->groupBy('product_id')
            ->orderByDesc('total') // Put the highest counts at the top.
            ->take(10)
            ->with('product:id,name,price,images,category_id', 'product.category:id,name')
            ->get();

        // Get categories for the filter dropdown.
        $categories = \App\Models\Category::all();

        // Send all data to the view.
        return view('admin.wishlists', compact('wishlists', 'topProducts', 'categories'));
    }
}
