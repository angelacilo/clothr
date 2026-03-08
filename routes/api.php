<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function () {

    // Dashboard
    Route::get('/stats', [App\Http\Controllers\Admin\AdminDashboardController::class, 'apiStats']);

    // Products
    Route::get('/products', function (Request $request) {
        $query = App\Models\Product::with(['category', 'inventory', 'images']);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        return $query->latest()->paginate(12);
    });

    Route::get('/products/{id}', function ($id) {
        return App\Models\Product::with(['category', 'inventory', 'images'])->findOrFail($id);
    });

    Route::delete('/products/{product}', [App\Http\Controllers\Admin\AdminProductController::class, 'destroy']);

    // Categories
    Route::get('/categories', function () {
        return App\Models\Category::all();
    });

    // Orders
    Route::get('/orders', function (Request $request) {
        $query = App\Models\Order::with(['user']);
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        return $query->latest()->paginate(15);
    });

    Route::get('/orders/{id}', function ($id) {
        return App\Models\Order::with(['user', 'items.product'])->findOrFail($id);
    });

    // Users
    Route::get('/users', function (Request $request) {
        $query = App\Models\User::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        return $query->latest()->paginate(15);
    });

    // Reviews
    Route::get('/reviews', function (Request $request) {
        $query = App\Models\Review::with(['user', 'product']);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        return $query->latest()->paginate(15);
    });

});