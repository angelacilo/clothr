<?php

/**
 * FILE: Shop/ProductController.php
 * 
 * What this file does:
 * This controller handles the individual Product Page (the details page).
 * It shows the product image, description, variants (colors/sizes), 
 * and handles the review system.
 * 
 * How it connects to the project:
 * - It is called when a customer clicks on a product from the shop or homepage.
 * - It checks if the logged-in customer is allowed to leave a review 
 *   (they must have bought the product and it must be delivered).
 * - It calculates the rating statistics (e.g., 4.5 stars based on 10 reviews).
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Shows the details page for a single product.
     * 
     * This function also handles the "Can the user review?" logic and 
     * gathers all the rating statistics for the star breakdown chart.
     * 
     * @param int $id — the ID of the product
     * @return view — the product details page
     */
    public function show($id)
    {
        // 1. Fetch the product details and its category from the database.
        $product = Product::with('category')->findOrFail($id);
        
        $canReview = false;
        $userReview = null;

        // 2. REVIEW PERMISSION CHECK:
        // Only allow a review if the user is logged in AND has already bought the item.
        if (auth()->check()) {
            $userId = auth()->id();
            
            // Check if the user already wrote a review for this product.
            $userReview = \App\Models\Review::where('product_id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$userReview) {
                // If they haven't reviewed yet, check if they have a "Delivered" order for this item.
                $hasOrdered = \App\Models\Order::where('user_id', $userId)
                    ->where('status', 'Delivered')
                    // whereJsonContains searches inside the JSON "items" column.
                    ->whereJsonContains('items', ['id' => (int) $id])
                    ->exists();

                // FALLBACK CHECK: If whereJsonContains fails, we loop through orders manually.
                if (!$hasOrdered) {
                    $deliveredOrders = \App\Models\Order::where('user_id', $userId)
                        ->where('status', 'Delivered')
                        ->get();
                    foreach ($deliveredOrders as $order) {
                        if (is_array($order->items)) {
                            foreach ($order->items as $item) {
                                if (isset($item['id']) && (int)$item['id'] === (int)$id) {
                                    $hasOrdered = true;
                                    break 2; // Break both loops.
                                }
                            }
                        }
                    }
                }
                $canReview = $hasOrdered;
            }
        }

        // 3. REVIEW STATISTICS:
        // Calculate the total number of visible reviews and the average star rating.
        $totalReviews = \App\Models\Review::where('product_id', $id)->where('is_visible', true)->count();
        $avgRating = \App\Models\Review::where('product_id', $id)->where('is_visible', true)->avg('rating') ?? 0;
        
        // Count how many people gave 5 stars, 4 stars, etc.
        $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $breakdown = \App\Models\Review::where('product_id', $id)->where('is_visible', true)
            ->select('rating', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get();
            
        foreach ($breakdown as $b) {
            $ratingCounts[$b->rating] = $b->count;
        }

        // Send all gathered data to the product view.
        return view('shop.product', compact('product', 'canReview', 'userReview', 'totalReviews', 'avgRating', 'ratingCounts'));
    }
}
