<?php

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
     * Get paginated reviews for a product (public)
     */
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->where('is_visible', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(5);

        $reviews->getCollection()->transform(function ($review) {
            $nameParts = explode(' ', trim($review->user->name));
            $firstName = $nameParts[0];
            $lastInitial = count($nameParts) > 1 ? substr(end($nameParts), 0, 1) . '.' : '';
            $displayName = trim("{$firstName} {$lastInitial}");

            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'reviewer_name' => $displayName,
                'created_at' => $review->created_at->format('F j, Y'),
                'is_own' => Auth::check() && $review->user_id === Auth::id(),
            ];
        });

        return response()->json($reviews);
    }

    /**
     * Store a new review
     */
    public function store(Request $request, $productId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = Auth::id();

        // Check if product exists
        $product = Product::findOrFail($productId);

        // Check if customer already reviewed
        $existingReview = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            return response()->json(['error' => 'You have already reviewed this product'], 403);
        }

        // Check if customer has a Delivered order containing this product
        $hasOrdered = Order::where('user_id', $userId)
            ->where('status', 'Delivered')
            ->whereJsonContains('items', ['id' => (int) $productId]) // Match the item structure exactly in JSON?
            ->exists();

        // Fallback for JSON querying issues: fetch all their delivered orders and check PHP-side
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

        $review = Review::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'rating' => $request->rating,
            'comment' => $request->comment ? strip_tags($request->comment) : null,
            'is_visible' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Review submitted successfully']);
    }

    /**
     * Update an existing review
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        $review->update([
            'rating' => $request->rating,
            'comment' => $request->comment ? strip_tags($request->comment) : null,
        ]);

        return response()->json(['success' => true, 'message' => 'Review updated successfully']);
    }

    /**
     * Delete an existing review
     */
    public function destroy($id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);
        $review->delete();

        return response()->json(['success' => true, 'message' => 'Review deleted successfully']);
    }
}
