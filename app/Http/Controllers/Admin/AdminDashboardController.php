<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return view('admin');
    }

    public function apiStats()
    {
        $totalRevenue = Order::where('order_status', 'delivered')->sum('total_amount') ?? 0;
        $todaysOrders = Order::whereDate('order_date', Carbon::today())->count();
        $avgOrderValue = Order::where('order_status', 'delivered')->avg('total_amount') ?? 0;
        $totalCustomers = User::where('role', 'user')->count();

        $orderStatus = [
            'pending'    => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'shipped'    => Order::where('order_status', 'shipped')->count(),
            'delivered'  => Order::where('order_status', 'delivered')->count(),
            'cancelled'  => Order::where('order_status', 'cancelled')->count(),
        ];

        $salesTrend = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $salesTrend['labels'][] = $date->format('M d');
            $salesTrend['data'][] = Order::where('order_status', 'delivered')
                ->whereDate('order_date', $date)
                ->sum('total_amount') ?? 0;
        }

        $lowStockCount = Product::whereHas('inventory', function($q) {
            $q->where('available_qty', '<=', 5);
        })->count();

        $totalProducts = Product::count();
        $totalOrdersCount = Order::count();
        $avgRating = Review::avg('rating') ?? 0;
        $activeUsers = User::where('role', 'user')->count();

        return response()->json([
            'totalRevenue'     => $totalRevenue,
            'todaysOrders'     => $todaysOrders,
            'avgOrderValue'    => $avgOrderValue,
            'totalCustomers'   => $totalCustomers,
            'orderStatus'      => $orderStatus,
            'salesTrend'       => $salesTrend,
            'categoryRevenue'  => ['labels' => [], 'data' => []],
            'topProducts'      => ['labels' => [], 'data' => []],
            'lowStockCount'    => $lowStockCount,
            'totalProducts'    => $totalProducts,
            'totalOrdersCount' => $totalOrdersCount,
            'avgRating'        => $avgRating,
            'activeUsers'      => $activeUsers,
        ]);
    }
}