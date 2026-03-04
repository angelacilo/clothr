<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Category;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ─────────────────────────────────────────────
// Admin API Routes
// ─────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Products
    Route::get('/products', [AdminProductController::class , 'apiIndex']);
    Route::post('/products', [AdminProductController::class , 'apiStore']);
    Route::get('/products/{product}', [AdminProductController::class , 'apiShow']);
    Route::put('/products/{product}', [AdminProductController::class , 'apiUpdate']);
    Route::delete('/products/{product}', [AdminProductController::class , 'apiDestroy']);

    // Categories
    Route::get('/categories', [AdminCategoryController::class , 'apiIndex']);
    Route::post('/categories', [AdminCategoryController::class , 'apiStore']);
    Route::put('/categories/{category}', [AdminCategoryController::class , 'apiUpdate']);
    Route::delete('/categories/{category}', [AdminCategoryController::class , 'apiDestroy']);

    // Orders
    Route::get('/orders', [AdminOrderController::class , 'apiIndex']);
    Route::get('/orders/{order}', [AdminOrderController::class , 'apiShow']);
    Route::put('/orders/{order}', [AdminOrderController::class , 'apiUpdate']);

    // Users
    Route::get('/users', [AdminUserController::class , 'apiIndex']);
    Route::put('/users/{user}/role', [AdminUserController::class , 'updateRole']);

    // Reviews
    Route::get('/reviews', [AdminReviewController::class , 'apiIndex']);
    Route::put('/reviews/{review}/approve', [AdminReviewController::class , 'approve']);
    Route::delete('/reviews/{review}', [AdminReviewController::class , 'apiDestroy']);

    // Reports
    Route::get('/reports', [AdminReportController::class , 'apiIndex']);
});

// ─────────────────────────────────────────────
// Shop API Routes (public + auth-guarded)
// ─────────────────────────────────────────────
Route::prefix('shop')->group(function () {

    // Products (public)
    Route::get('/products', [ProductController::class , 'apiIndex']);
    Route::get('/products/{slug}', [ProductController::class , 'apiShow']);

    // Categories (public)
    Route::get('/categories', function () {
            return Category::all();
        }
        );

        // Auth-required routes
        Route::middleware('auth')->group(function () {

            // Cart
            Route::get('/cart', [CartController::class , 'apiIndex']);
            Route::post('/cart', [CartController::class , 'apiAdd']);
            Route::put('/cart/{id}', [CartController::class , 'apiUpdate']);
            Route::delete('/cart/{id}', [CartController::class , 'apiRemove']);

            // Checkout
            Route::post('/checkout', [OrderController::class , 'apiCheckout']);

            // Orders
            Route::get('/orders', [OrderController::class , 'apiHistory']);
            Route::get('/orders/{id}', [OrderController::class , 'apiShow']);
        }
        );    });