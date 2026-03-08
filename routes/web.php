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

Route::get('/', function () {
    return view('welcome', ['page' => 'home']);
});

Route::get('/login', function () {
    return view('welcome', ['page' => 'login']);
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/orders', [\App\Http\Controllers\AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/products', [\App\Http\Controllers\AdminController::class, 'products'])->name('admin.products');
    Route::get('/archive', [\App\Http\Controllers\AdminController::class, 'archive'])->name('admin.archive');
    Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::get('/reviews', [\App\Http\Controllers\AdminController::class, 'reviews'])->name('admin.reviews');
    Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])->name('admin.reports');
    Route::get('/system-settings', [\App\Http\Controllers\AdminController::class, 'settings'])->name('admin.settings');
});
