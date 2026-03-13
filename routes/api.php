<?php
use App\Http\Controllers\Admin\AdminApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

Route::prefix("admin")->middleware(["auth:sanctum", "admin"])->group(function () {
    // Dashboard
    Route::get("/stats", [App\Http\Controllers\Admin\AdminDashboardController::class, "apiStats"]);

    // Products
    Route::get("/products", [AdminApiController::class, "products"]);
    Route::get("/products/{id}", [AdminApiController::class, "product"]);
    Route::delete("/products/{product}", [App\Http\Controllers\Admin\AdminProductController::class, "destroy"]);

    // Categories
    Route::get("/categories", [AdminApiController::class, "categories"]);

    // Orders
    Route::get("/orders", [AdminApiController::class, "orders"]);
    Route::get("/orders/{id}", [AdminApiController::class, "order"]);

    // Users
    Route::get("/users", [AdminApiController::class, "users"]);
    Route::delete("/users/{user}", [AdminApiController::class, "deleteUser"]);

    // Reviews
    Route::get("/reviews", [AdminApiController::class, "reviews"]);
    Route::delete("/reviews/{review}", [App\Http\Controllers\Admin\AdminReviewController::class, "destroy"]);

    // Reports
    Route::get("/reports/summary", [AdminApiController::class, "reportSummary"]);
    Route::get("/reports/top-products", [AdminApiController::class, "reportTopProducts"]);
    Route::get("/reports/orders-by-status", [AdminApiController::class, "reportOrdersByStatus"]);
});
