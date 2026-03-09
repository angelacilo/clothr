<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Authentication routes (throttled to prevent brute force)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:6,1');
});

// Public routes (shop)
Route::get('/products', [App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::get('/api/products/{id}', [App\Http\Controllers\ProductController::class, 'getDetails'])->name('products.details');

// Protected routes (including root /)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('home');
    });
    Route::get('/home', function () {
        return view('home');
    })->name('home');
    Route::get('/account', function () {
        return view('dashboard');
    })->name('account');
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Shopping Cart Routes
    Route::get('/cart', [App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}', [App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [App\Http\Controllers\CartController::class, 'getCount'])->name('cart.count');

    // Order Routes
    Route::get('/checkout', [App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [App\Http\Controllers\OrderController::class, 'store'])->name('checkout.store');
    Route::get('/order/{id}/confirmation', [App\Http\Controllers\OrderController::class, 'confirmation'])->name('order.confirmation');
    Route::get('/orders', [App\Http\Controllers\OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{id}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // Keep these POST/PUT/DELETE routes for React to submit data
    Route::post('products', [App\Http\Controllers\Admin\AdminProductController::class, 'store'])->name('admin.products.store');
    Route::put('products/{product}', [App\Http\Controllers\Admin\AdminProductController::class, 'update'])->name('admin.products.update');
    Route::delete('products/{product}', [App\Http\Controllers\Admin\AdminProductController::class, 'destroy'])->name('admin.products.destroy');

    Route::post('orders/{order}/status', [App\Http\Controllers\Admin\AdminOrderController::class, 'updateStatus']);
    Route::put('orders/{order}', [App\Http\Controllers\Admin\AdminOrderController::class, 'update'])->name('admin.orders.update');
    Route::delete('orders/{order}', [App\Http\Controllers\Admin\AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');

    Route::post('categories', [App\Http\Controllers\Admin\AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::put('categories/{category}', [App\Http\Controllers\Admin\AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('categories/{category}', [App\Http\Controllers\Admin\AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    Route::post('users', [App\Http\Controllers\Admin\AdminUserController::class, 'store'])->name('admin.users.store');
    Route::put('users/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('users/{user}', [App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('admin.users.destroy');
    Route::post('users/{user}/role', [App\Http\Controllers\Admin\AdminUserController::class, 'updateRole']);

    Route::post('reviews/{review}/approve', [App\Http\Controllers\Admin\AdminReviewController::class, 'approve']);
    Route::post('reviews/bulk-approve', [App\Http\Controllers\Admin\AdminReviewController::class, 'bulkApprove']);
    Route::delete('reviews/{review}', [App\Http\Controllers\Admin\AdminReviewController::class, 'destroy'])->name('admin.reviews.destroy');

    Route::get('reports/export', [App\Http\Controllers\Admin\AdminReportController::class, 'export'])->name('admin.reports.export');

    // Catch-all Ã¢â‚¬â€ sends ALL /admin/* GET requests to the single blade
    Route::get('/{any?}', function () {
        return view('admin');
    })->where('any', '.*')->name('admin.app');

});