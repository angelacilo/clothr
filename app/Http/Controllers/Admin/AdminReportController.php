<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class AdminReportController extends Controller
{
    /**
     * Display reports and analytics.
     */
    public function index()
    {
        // Monthly revenue (last 12 months)
        $monthlyRevenue = Order::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as revenue')
            ->where('order_status', 'completed')
            ->whereDate('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyLabels = [];
        $monthlyData = [];

        foreach ($monthlyRevenue as $revenue) {
            $monthlyLabels[] = $months[intval($revenue->month) - 1];
            $monthlyData[] = (float) $revenue->revenue;
        }

        // Order status distribution
        $ordersByStatus = [
            'pending' => Order::where('order_status', 'pending')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'shipped' => Order::where('order_status', 'shipped')->count(),
            'delivered' => Order::where('order_status', 'delivered')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        $statusLabels = array_keys($ordersByStatus);
        $statusData = array_values($ordersByStatus);

        // Top categories
        $topCategories = Category::withCount('products')
            ->orderByDesc('products_count')
            ->take(10)
            ->get();

        return view('admin.reports.index', compact(
            'monthlyLabels',
            'monthlyData',
            'statusLabels',
            'statusData',
            'topCategories'
        ));
    }

    /**
     * Export reports to CSV.
     */
    public function export()
    {
        $orders = Order::with(['user', 'items'])->get();

        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://memory', 'w');

        // Add CSV headers
        fputcsv($handle, ['Order ID', 'Customer Name', 'Email', 'Total Amount', 'Status', 'Date']);

        // Add data rows
        foreach ($orders as $order) {
            fputcsv($handle, [
                $order->order_id,
                $order->user->name ?? 'N/A',
                $order->user->email ?? 'N/A',
                $order->total_amount,
                $order->order_status,
                $order->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=$filename");
    }
}
