<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

class ReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user:id,name,email', 'product:id,name,images'])->latest()->paginate(20);
        return view('admin.reviews', compact('reviews'));
    }

    public function toggleVisibility($id)
    {
        $review = Review::findOrFail($id);
        $review->is_visible = !$review->is_visible;
        $review->save();

        return back()->with('success', 'Review visibility updated successfully');
    }
}
