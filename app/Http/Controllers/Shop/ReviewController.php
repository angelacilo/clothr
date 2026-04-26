<?php

/**
 * FILE: Shop/ReviewController.php
 * 
 * What this file does:
 * This controller handles the customer-facing side of product reviews.
 * It allows customers to read reviews, submit new ones, edit their own 
 * reviews, or delete them.
 * 
 * How it connects to the project:
 * - It is called by AJAX (background) requests when a customer interacts 
 *   with the reviews section on a product page.
 * - It uses the Review, Product, and Order models.
 * - It enforces a strict rule: "You can only review what you have bought".
 */

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Gets a list of reviews for a specific product.
     * 
     * This is used to load reviews dynamically (5 at a time) 
     * so the product page stays fast.
     * 
     * @param int $productId — the ID of the product
     * @return json — list of reviews with formatted reviewer names
     */
    public function index($productId)
    {
        // Fetch only visible reviews for this product, newest first.
        $reviews = Review::where('product_id', $productId)
            ->where('is_visible', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(5);

        // We "transform" the data to format the name (e.g., "John D.") for privacy.
        $reviews->getCollection()->transform(function ($review) {
            $nameParts = explode(' ', trim($review->user->name));
            $firstName = $nameParts[0];
            $lastInitial = count($nameParts) > 1 ? substr(end($nameParts), 0, 1) . '.' : '';
            $displayName = trim("{$firstName} {$lastInitial}");

            return [
                'id'            => $review->id,
                'rating'        => $review->rating,
                'comment'       => $review->comment,
                'reviewer_name' => $displayName,
                'created_at'    => $review->created_at->format('F j, Y'),
                // Check if the review belongs to the person currently looking at it.
                'is_own'        => Auth::check() && $review->user_id === Auth::id(),
            ];
        });

        return response()->json($reviews);
    }

    /**
     * Saves a brand new review from a customer.
     * 
     * @param Request $request — contains the rating and comment
     * @param int $productId — the ID of the product being reviewed
     * @return json — success or error message
     */
    public function store(Request $request, $productId)
    {
        // VALIDATION: Ensure rating is between 1 and 5 stars.
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        // 1. Ensure the product actually exists.
        $product = Product::findOrFail($productId);

        // 2. CHECK: Has the customer already reviewed this? (Only 1 review per product).
        $existingReview = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            return response()->json(['error' => 'You have already reviewed this product'], 403);
        }

        // 3. SECURITY CHECK: Did they actually buy and receive this item?
        // We look for a "Delivered" order containing this product ID in the JSON list.
        $hasOrdered = Order::where('user_id', $userId)
            ->where('status', 'Delivered')
            ->whereJsonContains('items', ['id' => (int) $productId])
            ->exists();

        // FALLBACK: If the JSON search fails, we check manually by looping through orders.
        if (!$hasOrdered) {
            $deliveredOrders = Order::where('user_id', $userId)
                ->where('status', 'Delivered')
                ->get();
                
            $foundInItems = false;
            foreach ($deliveredOrders as $order) {
                if (is_array($order->items)) {
                    foreach ($order->items as $item) {
                        if (isset($item['id']) && (int)$item['id'] === (int)$productId) {
                            $foundInItems = true;
                            break 2;
                        }
                    }
                }
            }
            if (!$foundInItems) {
                return response()->json(['error' => 'You can only review products you have purchased and received.'], 403);
            }
        }

        // 4. SAVE: Create the review record.
        $review = Review::create([
            'user_id'    => $userId,
            'product_id' => $productId,
            'rating'     => $request->rating,
            // strip_tags() removes any HTML code to prevent hackers from injecting scripts.
            'comment'    => $request->comment ? strip_tags($request->comment) : null,
            'is_visible' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Review submitted successfully']);
    }

    /**
     * Updates a review that the customer previously wrote.
     * 
     * @param Request $request — contains updated rating/comment
     * @param int $id — the ID of the review
     * @return json — success message
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Find the review, but ONLY if it belongs to the logged-in user.
        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        $review->update([
            'rating'  => $request->rating,
            'comment' => $request->comment ? strip_tags($request->comment) : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Review updated successfully']);
    }

    /**
     * Deletes a review.
     * 
     * @param int $id — the ID of the review
     * @return json — success message
     */
    public function destroy($id)
    {
        // Find the review, but ONLY if it belongs to the logged-in user.
        $review = Review::where('user_id', Auth::id())->findOrFail($id);
        $review->delete();

        return response()->json(['success' => true, 'message' => 'Review deleted successfully']);
    }
}
