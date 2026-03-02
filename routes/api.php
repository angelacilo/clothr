<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Product;
use App\Models\Category;
use App\Http\Controllers\Admin\AdminProductController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {

    Route::get('/products', function (Request $request) {
        $query = Product::with(['category', 'inventory', 'images']);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        return $query->latest()->paginate(12);
    });

    Route::delete('/products/{product}', [AdminProductController::class, 'destroy']);

    Route::get('/categories', function () {
        return Category::all();
    });

});