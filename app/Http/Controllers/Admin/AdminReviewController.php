<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Review;
class AdminReviewController extends Controller
{
    public function index()
    {
        $reviews = Review::with(['user', 'product'])->latest('created_at')->paginate(20);
        return view('admin.reviews.index', compact('reviews'));
    }
    public function destroy(Review $review)
    {
        $review->delete();
        return response()->json(['success' => true]);
    }
}
