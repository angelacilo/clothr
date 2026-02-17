<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Get dashboard statistics
     
    public function stats()
    {
        $today = Carbon::today();

        // Orders today
        $ordersToday = Order::whereDate('created_at', $today)->count();

        // Revenue today
        $revenueToday = Order::whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        // Top selling products
        $topProducts = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product')
            ->take(5)
            ->get();

        // Category performance
        $categoryPerformance = Category::withCount(['products as total_products'])
            ->get()
            ->map(function ($category) {
                $category->total_sold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.category_id', $category->id)
                    ->where('orders.payment_status', 'paid')
                    ->sum('order_items.quantity');
                return $category;
            });

        return response()->json([
            'orders_today' => $ordersToday,
            'revenue_today' => $revenueToday,
            'top_products' => $topProducts,
            'category_performance' => $categoryPerformance,
        ]);
    }
}
