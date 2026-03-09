<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', [\App\Http\Controllers\ShopController::class, 'index'])->name('home');
Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'shop'])->name('shop');
Route::get('/category/{slug}', [\App\Http\Controllers\ShopController::class, 'category'])->name('category');
Route::get('/product/{id}', [\App\Http\Controllers\ShopController::class, 'product'])->name('product');
Route::get('/cart', [\App\Http\Controllers\ShopController::class, 'cart'])->name('cart');
Route::get('/order-confirmation/{id}', [\App\Http\Controllers\ShopController::class, 'confirmation'])->name('order.confirmation');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'sendResetCode'])->name('password.code');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'postLogin'])->name('login.post');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'postRegister'])->name('register.post');
Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\ShopController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [\App\Http\Controllers\ShopController::class, 'placeOrder'])->name('place.order');
});

Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/orders', [\App\Http\Controllers\AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/products', [\App\Http\Controllers\AdminController::class, 'products'])->name('admin.products');
    Route::post('/products', [\App\Http\Controllers\AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/products/{id}', [\App\Http\Controllers\AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{id}', [\App\Http\Controllers\AdminController::class, 'deleteProduct'])->name('admin.products.delete');
    Route::post('/products/{id}/archive', [\App\Http\Controllers\AdminController::class, 'archiveProduct'])->name('admin.products.archive');
    
    Route::get('/categories', [\App\Http\Controllers\AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [\App\Http\Controllers\AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [\App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    Route::get('/archive', [\App\Http\Controllers\AdminController::class, 'archive'])->name('admin.archive');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/reviews', [\App\Http\Controllers\AdminController::class, 'reviews'])->name('admin.reviews');
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/system-settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
});
