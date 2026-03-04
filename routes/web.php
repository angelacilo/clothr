<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/* |-------------------------------------------------------------------------- | Web Routes |-------------------------------------------------------------------------- | | Here is where you can register web routes for your application. These | routes are loaded by the RouteServiceProvider within a group which | contains the "web" middleware group. Now create something great! | */

// Authentication routes (throttled to prevent brute force)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class , 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class , 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class , 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class , 'register'])->middleware('throttle:6,1');
});

// Public routes (shop)
Route::get('/products', function () {
    return view('shop.products.index'); })->name('products.index');
Route::get('/products/{slug}', function ($slug) {
    return view('shop.products.show', ['slug' => $slug]); })->name('products.show');
Route::get('/api/products/{id}', [App\Http\Controllers\ProductController::class , 'getDetails'])->name('products.details');

// Protected routes (including root /)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
            return view('shop.home');
        }
        );
        Route::get('/home', function () {
            return view('shop.home');
        }
        )->name('home');
        Route::get('/account', function () {
            return view('dashboard');
        }
        )->name('account');
        Route::get('/dashboard', function () {
            return view('dashboard');
        }
        )->name('dashboard');

        Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

        // Shopping Cart Routes
        Route::get('/cart', function () {
            return view('shop.cart.index'); }
        )->name('cart.index');
        Route::post('/cart/add', [App\Http\Controllers\CartController::class , 'add'])->name('cart.add');
        Route::post('/cart/update/{id}', [App\Http\Controllers\CartController::class , 'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}', [App\Http\Controllers\CartController::class , 'remove'])->name('cart.remove');
        Route::post('/cart/clear', [App\Http\Controllers\CartController::class , 'clear'])->name('cart.clear');
        Route::get('/cart/count', [App\Http\Controllers\CartController::class , 'getCount'])->name('cart.count');

        // Order Routes
        Route::get('/checkout', function () {
            return view('shop.checkout.index'); }
        )->name('checkout.index');
        Route::post('/checkout', [App\Http\Controllers\OrderController::class , 'store'])->name('checkout.store');
        Route::get('/order/{id}/confirmation', function ($id) {
            return view('shop.checkout.confirmation', ['orderId' => $id]); }
        )->name('order.confirmation');
        Route::get('/orders', function () {
            return view('shop.orders.history'); }
        )->name('orders.history');
        Route::get('/orders/{id}', function () {
            return view('shop.orders.show'); }
        )->name('orders.show');
    });

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [App\Http\Controllers\Admin\AdminDashboardController::class , 'index'])->name('admin.dashboard');

    // Products
    Route::resource('products', App\Http\Controllers\Admin\AdminProductController::class , ['names' => 'admin.products']);

    // Orders
    Route::resource('orders', App\Http\Controllers\Admin\AdminOrderController::class , ['names' => 'admin.orders']);
    Route::post('orders/{order}/status', [App\Http\Controllers\Admin\AdminOrderController::class , 'updateStatus']);

    // Categories
    Route::resource('categories', App\Http\Controllers\Admin\AdminCategoryController::class , ['names' => 'admin.categories']);

    // Users
    Route::resource('users', App\Http\Controllers\Admin\AdminUserController::class , ['names' => 'admin.users']);
    Route::post('users/{user}/role', [App\Http\Controllers\Admin\AdminUserController::class , 'updateRole']);

    // Reviews
    Route::resource('reviews', App\Http\Controllers\Admin\AdminReviewController::class , ['names' => 'admin.reviews']);
    Route::post('reviews/{review}/approve', [App\Http\Controllers\Admin\AdminReviewController::class , 'approve']);
    Route::post('reviews/bulk-approve', [App\Http\Controllers\Admin\AdminReviewController::class , 'bulkApprove']);

    // Reports
    Route::get('reports', [App\Http\Controllers\Admin\AdminReportController::class , 'index'])->name('admin.reports');
    Route::get('reports/export', [App\Http\Controllers\Admin\AdminReportController::class , 'export'])->name('admin.reports.export');
});
