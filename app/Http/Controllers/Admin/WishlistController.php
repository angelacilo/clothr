<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;

class WishlistController extends Controller
{
    public function wishlistIndex(Request $request)
    {
        $search = $request->get('search');
        $categoryId = $request->get('category_id');

        $query = Wishlist::with([
            'user:id,name,email',
            'product:id,name,price,images,category_id',
            'product.category:id,name'
        ]);

        if ($search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('product', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->whereHas('product', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $wishlists = $query->latest()->paginate(20)->withQueryString();

        $topProducts = Wishlist::select('product_id')
            ->selectRaw('count(*) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(10)
            ->with('product:id,name,price,images,category_id', 'product.category:id,name')
            ->get();

        $categories = \App\Models\Category::all();

        return view('admin.wishlists', compact('wishlists', 'topProducts', 'categories'));
    }
}
