<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function show($id)
    {
        $product = Product::with('category', 'reviews.user')->findOrFail($id);
        return view('shop.product', compact('product'));
    }
}
