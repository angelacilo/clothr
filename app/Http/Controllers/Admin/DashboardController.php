<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_sales' => Order::sum('total'),
            'orders' => Order::count(),
            'products' => Product::where('isArchived', false)->count(),
            'customers' => User::where('is_admin', false)->count(),
        ];

        $statusCounts = [
            'pending' => Order::where('status', 'Pending')->count(),
            'processing' => Order::where('status', 'Processing')->count(),
            'shipped' => Order::where('status', 'Shipped')->count(),
            'delivered' => Order::where('status', 'Delivered')->count(),
            'cancelled' => Order::where('status', 'Cancelled')->count(),
        ];

        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('total');
        $pendingActions = Order::where('status', 'Pending')->count();
        $processingCount = Order::where('status', 'Processing')->count();

        $dailySales = [];
        $dailyLabels = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('M d');
            $dailySales[] = Order::whereDate('created_at', $date)->sum('total');
        }

        $categories = Category::where('isVisible', true)->get();
        $categoryLabels = [];
        $categoryRevenue = [];
        $catRevenueMap = [];
        
        foreach ($categories as $cat) {
            $categoryLabels[] = $cat->name;
            $catRevenueMap[$cat->name] = 0;
        }

        $allOrdersForCat = Order::all();
        foreach ($allOrdersForCat as $order) {
            $items = is_array($order->items) ? $order->items : [];
            foreach ($items as $item) {
                $catName = $item['category'] ?? '';
                if (isset($catRevenueMap[$catName])) {
                    $catRevenueMap[$catName] += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                }
            }
        }

        foreach ($categoryLabels as $label) {
            $categoryRevenue[] = $catRevenueMap[$label];
        }

        $lowStockCount = Product::where('isArchived', false)->where('stock', '<', 10)->count();
        $recent_orders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'statusCounts', 'todayOrders', 'todayRevenue',
            'pendingActions', 'processingCount', 'dailySales', 'dailyLabels',
            'categoryLabels', 'categoryRevenue', 'lowStockCount', 'recent_orders'
        ));
    }
}
