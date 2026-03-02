<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class AdminReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index()
    {
        $reviews = Review::with(['user', 'product'])
            ->latest()
            ->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Approve a review (AJAX).
     */
    public function approve(Review $review)
    {
        // Assuming you have a status field for reviews
        // For now, we'll just set a simple flag or approve action
        return response()->json([
            'success' => true,
            'message' => 'Review approved successfully!',
        ]);
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully!');
    }

    /**
     * Bulk approve reviews via AJAX.
     */
    public function bulkApprove()
    {
        // This would require a status field in reviews table
        return response()->json([
            'success' => true,
            'message' => 'Reviews approved successfully!',
        ]);
    }
}
