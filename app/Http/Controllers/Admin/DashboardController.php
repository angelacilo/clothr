<?php

/**
 * FILE: Admin/DashboardController.php
 * 
 * What this file does:
 * This controller handles the main dashboard for the admin.
 * It calculates all the statistics (Total Sales, Order counts, etc.)
 * and prepares data for the charts on the dashboard page.
 * 
 * How it connects to the project:
 * - It is called by the route "admin.dashboard" in routes/admin.php.
 * - It talks to the Order, Product, User, and Category models to get data.
 * - It returns the "admin.dashboard" view.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;

class DashboardController extends Controller
{
    /**
     * Shows the main admin dashboard page.
     * 
     * This function calculates all the important numbers (Revenue, Sales)
     * and prepares the lists of recent orders and low stock items.
     * 
     * @return view — the dashboard page with all stats
     */
    public function index()
    {
        // Calculate main statistics
        $stats = [
            // sum('total') adds up the price of every order ever made.
            'total_sales' => Order::sum('total'),
            // count() simply counts how many rows are in the orders table.
            'orders' => Order::count(),
            // Only count products that are NOT archived (hidden).
            'products' => Product::where('isArchived', false)->count(),
            // Only count users who are NOT admins.
            'customers' => User::where('is_admin', false)->count(),
        ];

        // Group orders by their current status (Pending, Processing, etc.)
        $statusCounts = [
            'pending' => Order::where('status', 'Pending')->count(),
            'processing' => Order::where('status', 'Processing')->count(),
            'shipped' => Order::where('status', 'Shipped')->count(),
            'delivered' => Order::where('status', 'Delivered')->count(),
            'cancelled' => Order::where('status', 'Cancelled')->count(),
        ];

        // Get numbers specifically for TODAY
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('total');
        
        // Count how many orders need attention right now
        $pendingActions = Order::where('status', 'Pending')->count();
        $processingCount = Order::where('status', 'Processing')->count();

        // Prepare data for the 7-day Sales Chart
        $dailySales = [];
        $dailyLabels = [];
        // Loop backwards from 7 days ago to today.
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i);
            // Format like "Oct 25"
            $dailyLabels[] = $date->format('M d');
            // Add up revenue for this specific day.
            $dailySales[] = Order::whereDate('created_at', $date)->sum('total');
        }

        // Prepare data for the Category Revenue Chart
        $categories = Category::where('isVisible', true)->get();
        $categoryLabels = [];
        $categoryRevenue = [];
        $catRevenueMap = [];
        
        // Initialize the map with 0 for every category.
        foreach ($categories as $cat) {
            $categoryLabels[] = $cat->name;
            $catRevenueMap[$cat->name] = 0;
        }

        // Loop through ALL orders and add up revenue per category.
        // This is done because one order might contain products from different categories.
        $allOrdersForCat = Order::all();
        foreach ($allOrdersForCat as $order) {
            // "items" is a JSON column, so we check if it's an array first.
            $items = is_array($order->items) ? $order->items : [];
            foreach ($items as $item) {
                $catName = $item['category'] ?? '';
                // If the category exists in our map, add the item's total price.
                if (isset($catRevenueMap[$catName])) {
                    $catRevenueMap[$catName] += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                }
            }
        }

        // Convert our map into a simple list for the chart.
        foreach ($categoryLabels as $label) {
            $categoryRevenue[] = $catRevenueMap[$label];
        }

        // Count products that are running low (less than 10 units).
        $lowStockCount = Product::where('isArchived', false)->where('stock', '<', 10)->count();
        
        // Get the 5 most recent orders to show in the dashboard table.
        // orderBy('created_at', 'desc') puts the newest ones at the top.
        $recent_orders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        // Send all this gathered data to the dashboard view.
        return view('admin.dashboard', compact(
            'stats', 'statusCounts', 'todayOrders', 'todayRevenue',
            'pendingActions', 'processingCount', 'dailySales', 'dailyLabels',
            'categoryLabels', 'categoryRevenue', 'lowStockCount', 'recent_orders'
        ));
    }
}
