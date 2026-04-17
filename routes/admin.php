<?php

use Illuminate\Support\Facades\Route;

// Ensure this file is loaded
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin'], 'as' => 'admin.'], function () {
    
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    
    // Orders
    Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.details');
    Route::put('/orders/{id}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.status');
    Route::put('/orders/{id}/courier', [\App\Http\Controllers\Admin\OrderController::class, 'updateCourier'])->name('orders.courier');
    
    // Products
    Route::get('/products', [\App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products');
    Route::post('/products', [\App\Http\Controllers\Admin\ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [\App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.delete');
    Route::post('/products/{id}/archive', [\App\Http\Controllers\Admin\ProductController::class, 'archive'])->name('products.archive');
    Route::post('/products/{id}/restore', [\App\Http\Controllers\Admin\ProductController::class, 'restore'])->name('products.restore');
    Route::get('/archive', [\App\Http\Controllers\Admin\ProductController::class, 'archivedIndex'])->name('archive');
    
    // Categories
    Route::get('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories');
    Route::post('/categories', [\App\Http\Controllers\Admin\CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [\App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.delete');
    
    // Users
    Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users');
    
    // Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    
    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews');
    Route::post('/reviews/{id}/toggle', [\App\Http\Controllers\Admin\ReviewController::class, 'toggleVisibility'])->name('reviews.toggle');
    
    // Wishlists
    Route::get('/wishlists', [\App\Http\Controllers\Admin\WishlistController::class, 'wishlistIndex'])->name('wishlists');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings');
    Route::post('/profile/update', [\App\Http\Controllers\Admin\SettingsController::class, 'updateProfile'])->name('profile.update');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Courier Management
    Route::post('/couriers', [\App\Http\Controllers\Admin\AdminController::class, 'storeCourier'])->name('couriers.store');

});
