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
Route::get('/info/{slug}', [\App\Http\Controllers\ShopController::class, 'info'])->name('info');
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

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\ShopController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [\App\Http\Controllers\ShopController::class, 'placeOrder'])->name('place.order');
    
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/orders', [\App\Http\Controllers\ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/addresses', [\App\Http\Controllers\ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profile/addresses/{id}/default', [\App\Http\Controllers\ProfileController::class, 'setDefaultAddress'])->name('profile.addresses.default');
    Route::delete('/profile/addresses/{id}', [\App\Http\Controllers\ProfileController::class, 'deleteAddress'])->name('profile.addresses.delete');
    Route::get('/profile/wishlist', [\App\Http\Controllers\ProfileController::class, 'wishlist'])->name('profile.wishlist');
    Route::get('/profile/settings', [\App\Http\Controllers\ProfileController::class, 'settings'])->name('profile.settings');
    Route::post('/profile/update', [\App\Http\Controllers\ProfileController::class, 'updateProfile'])->name('profile.update');
    
    Route::post('/wishlist/toggle/{id}', [\App\Http\Controllers\ProfileController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Profile: Reviews & Order Details
    Route::get('/profile/reviews', [\App\Http\Controllers\ProfileController::class, 'reviews'])->name('profile.reviews');
    Route::get('/profile/order/{id}', [\App\Http\Controllers\ProfileController::class, 'orderDetails'])->name('profile.order');

    // Location APIs
    Route::get('/api/countries', [\App\Http\Controllers\LocationController::class, 'getCountries']);
    Route::get('/api/regions/{country_id}', [\App\Http\Controllers\LocationController::class, 'getRegions']);
    Route::get('/api/cities/{region_id}', [\App\Http\Controllers\LocationController::class, 'getCities']);

    // Cart APIs
    Route::get('/api/cart', [\App\Http\Controllers\CartController::class, 'getCart']);
    Route::post('/api/cart/sync', [\App\Http\Controllers\CartController::class, 'sync']);
    Route::post('/api/cart/update', [\App\Http\Controllers\CartController::class, 'updateItem']);
    Route::post('/api/cart/remove', [\App\Http\Controllers\CartController::class, 'removeItem']);
});

Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/orders', [\App\Http\Controllers\AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/orders/{id}', [\App\Http\Controllers\AdminController::class, 'orderDetails'])->name('admin.orders.details');
    Route::put('/orders/{id}/status', [\App\Http\Controllers\AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
    Route::put('/orders/{id}/courier', [\App\Http\Controllers\AdminController::class, 'updateOrderCourier'])->name('admin.orders.courier');
    Route::get('/products', [\App\Http\Controllers\AdminController::class, 'products'])->name('admin.products');
    Route::post('/products', [\App\Http\Controllers\AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::put('/products/{id}', [\App\Http\Controllers\AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{id}', [\App\Http\Controllers\AdminController::class, 'deleteProduct'])->name('admin.products.delete');
    Route::post('/products/{id}/archive', [\App\Http\Controllers\AdminController::class, 'archiveProduct'])->name('admin.products.archive');
    Route::post('/products/{id}/restore', [\App\Http\Controllers\AdminController::class, 'restoreProduct'])->name('admin.products.restore');
    
    Route::get('/categories', [\App\Http\Controllers\AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/categories', [\App\Http\Controllers\AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::put('/categories/{id}', [\App\Http\Controllers\AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{id}', [\App\Http\Controllers\AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    Route::get('/archive', [\App\Http\Controllers\AdminController::class, 'archive'])->name('admin.archive');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/reviews', [\App\Http\Controllers\AdminController::class, 'reviews'])->name('admin.reviews');
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/system-settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
    Route::post('/profile/update', [\App\Http\Controllers\AdminController::class, 'updateProfile'])->name('admin.profile.update');
});
