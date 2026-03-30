<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        // Don't eager load all reviews here, we'll fetch them via AJAX
        $product = Product::with('category')->findOrFail($id);
        
        $canReview = false;
        $userReview = null;

        if (auth()->check()) {
            $userId = auth()->id();
            
            $userReview = \App\Models\Review::where('product_id', $id)
                ->where('user_id', $userId)
                ->first();

            if (!$userReview) {
                // Determine if user has a delivered order for this product
                $hasOrdered = \App\Models\Order::where('user_id', $userId)
                    ->where('status', 'Delivered')
                    ->whereJsonContains('items', ['id' => (int) $id])
                    ->exists();

                if (!$hasOrdered) {
                    // Fallback JSON parsing just in case order query fails:
                    $deliveredOrders = \App\Models\Order::where('user_id', $userId)
                        ->where('status', 'Delivered')
                        ->get();
                    foreach ($deliveredOrders as $order) {
                        if (is_array($order->items)) {
                            foreach ($order->items as $item) {
                                if (isset($item['id']) && (int)$item['id'] === (int)$id) {
                                    $hasOrdered = true;
                                    break 2;
                                }
                            }
                        }
                    }
                }
                $canReview = $hasOrdered;
            }
        }

        // Review Statistics
        $totalReviews = \App\Models\Review::where('product_id', $id)->where('is_visible', true)->count();
        $avgRating = \App\Models\Review::where('product_id', $id)->where('is_visible', true)->avg('rating') ?? 0;
        
        $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $breakdown = \App\Models\Review::where('product_id', $id)->where('is_visible', true)
            ->select('rating', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->get();
            
        foreach ($breakdown as $b) {
            $ratingCounts[$b->rating] = $b->count;
        }

        return view('shop.product', compact('product', 'canReview', 'userReview', 'totalReviews', 'avgRating', 'ratingCounts'));
    }
}
