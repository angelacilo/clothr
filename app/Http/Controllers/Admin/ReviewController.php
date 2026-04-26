<?php

/**
 * FILE: Admin/ReviewController.php
 * 
 * What this file does:
 * This controller allows the admin to manage customer reviews.
 * The admin can see what customers are saying about products and 
 * hide reviews that might be spam or inappropriate.
 * 
 * How it connects to the project:
 * - It is called by routes in routes/admin.php.
 * - It uses the Review model to access customer feedback.
 * - The view it returns is resources/views/admin/reviews.blade.php.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Shows the product reviews management page.
     * 
     * @return view — the reviews list page
     */
    public function index()
    {
        // Fetch all reviews, 20 per page.
        // "with" is used to get the user name and product name in one query.
        $reviews = Review::with(['user:id,name,email', 'product:id,name,images'])
            ->latest()
            ->paginate(20);
            
        return view('admin.reviews', compact('reviews'));
    }

    /**
     * Shows or Hides a review on the public website.
     * 
     * This is useful for moderating content.
     * 
     * @param int $id — the ID of the review
     * @return redirect — back to the reviews list
     */
    public function toggleVisibility($id)
    {
        // Find the specific review.
        $review = Review::findOrFail($id);
        
        // Flip the boolean value. If it was true (1), it becomes false (0).
        $review->is_visible = !$review->is_visible;
        
        // Save the change to the database.
        $review->save();

        return back()->with('success', 'Review visibility updated successfully');
    }
}
