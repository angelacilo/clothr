<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
    return $request->user();
});

Route::prefix("admin")->group(function () {

    // Dashboard
    Route::get("/stats", [App\Http\Controllers\Admin\AdminDashboardController::class, "apiStats"]);

    // Products
    Route::get("/products", function (Request $request) {
        $query = App\Models\Product::with(["category", "inventory", "images"]);
        if ($request->filled("search")) {
            $query->where("name", "like", "%" . $request->search . "%");
        }
        if ($request->filled("category")) {
            $query->where("category_id", $request->category);
        }
        return $query->latest()->paginate(12);
    });
    Route::get("/products/{id}", function ($id) {
        return App\Models\Product::with(["category", "inventory", "images"])->findOrFail($id);
    });
    Route::delete("/products/{product}", [App\Http\Controllers\Admin\AdminProductController::class, "destroy"]);

    // Categories
    Route::get("/categories", function () {
        return App\Models\Category::withCount("products")->get();
    });

    // Orders
    Route::get("/orders", function (Request $request) {
        $query = App\Models\Order::with(["user"]);
        if ($request->filled("status")) {
            $query->where("order_status", $request->status);
        }
        return $query->latest()->paginate(15);
    });
    Route::get("/orders/{id}", function ($id) {
        return App\Models\Order::with(["user", "items.product"])->findOrFail($id);
    });

    // Users
    Route::get("/users", function (Request $request) {
        $query = App\Models\User::query();
        if ($request->filled("search")) {
            $query->where(function($q) use ($request) {
                $q->where("name", "like", "%" . $request->search . "%")
                  ->orWhere("email", "like", "%" . $request->search . "%");
            });
        }
        if ($request->filled("role")) $query->where("role", $request->role);
        return $query->latest()->paginate(15);
    });
    Route::delete("/users/{user}", function (App\Models\User $user) {
        if ($user->role === "admin") return response()->json(["success" => false, "message" => "Cannot delete admin"], 422);
        $user->delete();
        return response()->json(["success" => true]);
    });

    // Reviews
    Route::get("/reviews", function (Request $request) {
        $query = App\Models\Review::with(["user", "product"])->latest("created_at");
        if ($request->filled("rating")) $query->where("rating", $request->rating);
        return $query->paginate(15);
    });
    Route::delete("/reviews/{review}", [App\Http\Controllers\Admin\AdminReviewController::class, "destroy"]);

    // Reports
    Route::get("/reports/summary", function (Request $request) {
        $range = $request->get("range", "all");
        $query = App\Models\Order::query();
        if ($range === "today") $query->whereDate("order_date", today());
        elseif ($range === "week") $query->whereBetween("order_date", [now()->startOfWeek(), now()->endOfWeek()]);
        elseif ($range === "month") $query->whereMonth("order_date", now()->month)->whereYear("order_date", now()->year);
        elseif ($range === "year") $query->whereYear("order_date", now()->year);
        $orders = (clone $query)->whereNotIn("order_status", ["cancelled"])->get();
        $lowStock = App\Models\Inventory::with("product.category")->where("available_qty", "<=", 5)->get()
            ->map(fn($i) => [
                "product_id"    => $i->product_id,
                "name"          => $i->product->name ?? "-",
                "category_name" => $i->product->category->category_name ?? "-",
                "available_qty" => $i->available_qty,
            ]);
        return response()->json([
            "total_revenue"   => $orders->sum("total_amount"),
            "total_orders"    => $orders->count(),
            "avg_order_value" => $orders->count() ? $orders->avg("total_amount") : 0,
            "total_discounts" => $orders->sum("discount_amount"),
            "low_stock"       => $lowStock,
        ]);
    });
    Route::get("/reports/top-products", function (Request $request) {
        $range = $request->get("range", "all");
        $query = DB::table("order_items")
            ->join("products", "order_items.product_id", "=", "products.product_id")
            ->join("orders", "order_items.order_id", "=", "orders.order_id")
            ->leftJoin("categories", "products.category_id", "=", "categories.category_id")
            ->whereNotIn("orders.order_status", ["cancelled"])
            ->select("products.product_id", "products.name", "categories.category_name",
                DB::raw("SUM(order_items.quantity) as total_sold"),
                DB::raw("SUM(order_items.quantity * order_items.price) as total_revenue"));
        if ($range === "today") $query->whereDate("orders.order_date", today());
        elseif ($range === "week") $query->whereBetween("orders.order_date", [now()->startOfWeek(), now()->endOfWeek()]);
        elseif ($range === "month") $query->whereMonth("orders.order_date", now()->month)->whereYear("orders.order_date", now()->year);
        elseif ($range === "year") $query->whereYear("orders.order_date", now()->year);
        return $query->groupBy("products.product_id", "products.name", "categories.category_name")
            ->orderByDesc("total_sold")->limit(10)->get();
    });
    Route::get("/reports/orders-by-status", function () {
        return DB::table("orders")->select("order_status", DB::raw("COUNT(*) as count"))->groupBy("order_status")->get();
    });

});
