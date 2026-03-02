<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Key statistics
        $totalRevenue = Order::where('order_status', 'completed')->sum('total_amount');
        $todaysOrders = Order::whereDate('created_at', today())->count();
        $avgOrderValue = Order::where('order_status', 'completed')->avg('total_amount') ?? 0;
        $totalCustomers = User::where('role', 'customer')->count();

        // Order status breakdown
        $pending = Order::where('order_status', 'pending')->count();
        $processing = Order::where('order_status', 'processing')->count();
        $shipped = Order::where('order_status', 'shipped')->count();
        $delivered = Order::where('order_status', 'delivered')->count();
        $cancelled = Order::where('order_status', 'cancelled')->count();

        // Sales trend (last 8 days)
        $salesData = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->where('order_status', 'completed')
            ->whereDate('created_at', '>=', now()->subDays(8))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesTrendData = [];
        $salesTrendLabels = [];
        foreach ($salesData as $data) {
            $salesTrendLabels[] = $data->date;
            $salesTrendData[] = (float) $data->total;
        }

        // Revenue by category (simplified query)
        $categories = Category::with('products.orderItems')->get();
        $categoryRevenue = $categories->map(function ($category) {
            $revenue = 0;
            foreach ($category->products as $product) {
                foreach ($product->orderItems as $orderItem) {
                    $revenue += $orderItem->quantity * $orderItem->price;
                }
            }
            return [
                'name' => $category->category_name,
                'revenue' => $revenue
            ];
        });

        $categoryLabels = $categoryRevenue->pluck('name')->toArray();
        $categoryRevenueData = $categoryRevenue->pluck('revenue')->toArray();

        // Top selling products
        $topProducts = Product::withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(6)
            ->get()
            ->map(function ($product) {
                return [
                    'name' => $product->name,
                    'count' => $product->order_items_count
                ];
            });

        $topProductLabels = $topProducts->pluck('name')->toArray();
        $topProductData = $topProducts->pluck('count')->toArray();

        // Low stock warning
        $lowStockCount = Inventory::where('available_qty', '<', 10)->count();

        // Additional stats
        $totalProducts = Product::count();
        $totalOrdersCount = Order::count();
        $avgRating = Review::avg('rating') ?? 0;
        $activeUsers = User::where('role', 'customer')->count();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todaysOrders',
            'avgOrderValue',
            'totalCustomers',
            'pending',
            'processing',
            'shipped',
            'delivered',
            'cancelled',
            'salesTrendLabels',
            'salesTrendData',
            'categoryLabels',
            'categoryRevenueData',
            'topProductLabels',
            'topProductData',
            'lowStockCount',
            'totalProducts',
            'totalOrdersCount',
            'avgRating',
            'activeUsers'
        ));
    }
}
