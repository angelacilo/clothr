<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
*/

// Primary Redirect Safety Net
Route::get('/home', function() {
    if (!auth()->check()) return redirect()->route('login');
    
    switch (auth()->user()->role) {
        case 'admin':   return redirect()->route('admin.dashboard');
        case 'courier': return redirect()->route('courier.dashboard');
        case 'rider':   return redirect()->route('rider.dashboard');
        default:        return redirect()->route('home'); // which is /
    }
})->middleware('auth');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () { return view('auth.login'); })->name('login');
    Route::post('/login', [\App\Http\Controllers\AuthController::class, 'postLogin'])
        ->middleware('throttle:5,1')
        ->name('login.post');
        
    Route::post('/register', [\App\Http\Controllers\AuthController::class, 'postRegister'])->name('register.post');
    Route::post('/forgot-password', [\App\Http\Controllers\AuthController::class, 'sendResetCode'])->name('password.code');
});

// Logout (POST ONLY)
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Public Pages
Route::get('/', [\App\Http\Controllers\Shop\HomeController::class, 'index'])->name('home');
Route::get('/shop', [\App\Http\Controllers\Shop\ShopController::class, 'index'])->name('shop');
Route::get('/category/{slug}', [\App\Http\Controllers\Shop\ShopController::class, 'category'])->name('category');
Route::get('/product/{id}', [\App\Http\Controllers\Shop\ProductController::class, 'show'])->name('product');
Route::get('/cart', [\App\Http\Controllers\Shop\CartController::class, 'index'])->name('cart');
Route::get('/info/{slug}', [\App\Http\Controllers\Shop\HomeController::class, 'info'])->name('info');
Route::get('/product/{id}/reviews', [\App\Http\Controllers\Shop\ReviewController::class, 'index'])->name('product.reviews');
// Authenticated Shop Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [\App\Http\Controllers\Shop\OrderController::class, 'checkout'])->name('checkout');
    Route::post('/place-order', [\App\Http\Controllers\Shop\OrderController::class, 'placeOrder'])->name('place.order');
    Route::get('/order-confirmation/{id}', [\App\Http\Controllers\Shop\OrderController::class, 'confirmation'])->name('order.confirmation');
    
    // Profile
    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/', [\App\Http\Controllers\Profile\ProfileController::class, 'index'])->name('index');
        Route::get('/orders', [\App\Http\Controllers\Profile\ProfileController::class, 'orders'])->name('orders');
        Route::get('/order/{id}', [\App\Http\Controllers\Profile\ProfileController::class, 'orderDetails'])->name('order');
        Route::get('/addresses', [\App\Http\Controllers\Profile\ProfileController::class, 'addresses'])->name('addresses');
        Route::post('/addresses/{id}/default', [\App\Http\Controllers\Profile\ProfileController::class, 'setDefaultAddress'])->name('addresses.default');
        Route::delete('/addresses/{id}', [\App\Http\Controllers\Profile\ProfileController::class, 'deleteAddress'])->name('addresses.delete');
        Route::get('/wishlist', [\App\Http\Controllers\Profile\ProfileController::class, 'wishlist'])->name('wishlist');
        Route::get('/settings', [\App\Http\Controllers\Profile\ProfileController::class, 'settings'])->name('settings');
        Route::post('/update', [\App\Http\Controllers\Profile\ProfileController::class, 'updateProfile'])->name('update');
        Route::get('/reviews', [\App\Http\Controllers\Profile\ProfileController::class, 'reviews'])->name('reviews');
    });

    Route::post('/wishlist/toggle/{id}', [\App\Http\Controllers\Profile\ProfileController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Cart APIs
    Route::group(['prefix' => 'api/cart'], function () {
        Route::get('/', [\App\Http\Controllers\Shop\CartController::class, 'getCart']);
        Route::post('/sync', [\App\Http\Controllers\Shop\CartController::class, 'sync']);
        Route::post('/update', [\App\Http\Controllers\Shop\CartController::class, 'updateItem']);
        Route::post('/remove', [\App\Http\Controllers\Shop\CartController::class, 'removeItem']);
    });

    // Location APIs
    Route::get('/api/countries', [\App\Http\Controllers\Shop\LocationController::class, 'getCountries']);
    Route::get('/api/regions/{country_id}', [\App\Http\Controllers\Shop\LocationController::class, 'getRegions']);
    Route::get('/api/cities/{region_id}', [\App\Http\Controllers\Shop\LocationController::class, 'getCities']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Shop\NotificationController::class, 'getNotifications'])->name('user.notifications');
    Route::post('/notifications/read-all', [\App\Http\Controllers\Shop\NotificationController::class, 'markAllAsRead'])->name('user.notifications.read-all');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Shop\NotificationController::class, 'markAsRead'])->name('user.notifications.read');
    Route::post('/product/{id}/review', [\App\Http\Controllers\Shop\ReviewController::class, 'store'])->name('review.store');
    Route::put('/review/{id}', [\App\Http\Controllers\Shop\ReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/{id}', [\App\Http\Controllers\Shop\ReviewController::class, 'destroy'])->name('review.destroy');
});

use App\Http\Controllers\Courier\AuthController as CourierAuthController;
use App\Http\Controllers\Rider\AuthController as RiderAuthController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\RiderController;

// ─── COURIER PORTAL ──────────────────────────────────────
Route::prefix('courier')->name('courier.')->group(function () {

    // Guest-only auth routes
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [CourierAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [CourierAuthController::class, 'login']);
    });
    Route::post('/logout', [CourierAuthController::class, 'logout'])->name('logout');

    // Protected courier routes
    Route::middleware(['auth', 'role:courier'])->group(function () {
        Route::get('/dashboard',                      [CourierController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders',                         [CourierController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}',                 [CourierController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/assign-rider',   [CourierController::class, 'assignRider'])->name('assign-rider');
        Route::post('/orders/{order}/update-status',  [CourierController::class, 'updateStatus'])->name('update-status');
        Route::post('/orders/{order}/report-lost',    [CourierController::class, 'reportLost'])->name('report-lost');
        Route::get('/riders',                         [CourierController::class, 'riders'])->name('riders');
        Route::post('/riders',                        [CourierController::class, 'storeRider'])->name('riders.store');
    });
});

// ─── RIDER PORTAL ────────────────────────────────────────
Route::prefix('rider')->name('rider.')->group(function () {

    // Guest-only auth routes
    Route::middleware('guest')->group(function () {
        Route::get('/login',  [RiderAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [RiderAuthController::class, 'login']);
    });
    Route::post('/logout', [RiderAuthController::class, 'logout'])->name('logout');

    // Protected rider routes
    Route::middleware(['auth', 'role:rider'])->group(function () {
        Route::get('/dashboard',                              [RiderController::class, 'dashboard'])->name('dashboard');
        Route::get('/deliveries',                             [RiderController::class, 'deliveries'])->name('deliveries');
        Route::get('/deliveries/{delivery}',                  [RiderController::class, 'show'])->name('deliveries.show');
        Route::post('/deliveries/{delivery}/update-status',   [RiderController::class, 'updateStatus'])->name('update-status');
        Route::post('/toggle-availability',                   [RiderController::class, 'toggleAvailability'])->name('toggle-availability');
    });
});

require __DIR__ . '/admin.php';
