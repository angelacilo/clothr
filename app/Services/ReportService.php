<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;

class ReportService
{
    public function getAllReports()
    {
        $orders = Order::all();
        $productsMap = Product::with('category')->get()->keyBy('id');
        $allProducts = Product::with('category')->get();
        $users = User::where('is_admin', false)->with('orders')->get();

        return [
            'Sales Report' => $this->getSalesReport($orders, $productsMap),
            'Inventory Report' => $this->getInventoryReport($allProducts),
            'Customer Report' => $this->getCustomerReport($users),
            'Performance Report' => $this->getPerformanceReport($orders, $users),
            'Financial Report' => $this->getFinancialReport($orders),
            'Reviews Report' => $this->getReviewsReport(),
        ];
    }

    private function getSalesReport($orders, $productsMap)
    {
        $salesData = [];
        foreach ($orders as $order) {
            $items = is_array($order->items) ? $order->items : [];
            foreach ($items as $item) {
                $pId = $item['id'] ?? 0;
                $categoryName = $productsMap->has($pId) ? ($productsMap->get($pId)->category->name ?? 'N/A') : ($item['category'] ?? 'N/A');
                
                $salesData[] = [
                    'Order ID' => '#' . (1000 + $order->id),
                    'Product' => $item['name'] ?? 'Unknown',
                    'Category' => $categoryName,
                    'Quantity' => $item['quantity'] ?? 1,
                    'Unit Price' => 'P' . number_format($item['price'] ?? 0, 2),
                    'Total' => 'P' . number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2),
                    'Date' => $order->created_at->format('Y-m-d')
                ];
            }
        }
        return ['headers' => ['Order ID', 'Product', 'Category', 'Quantity', 'Unit Price', 'Total', 'Date'], 'rows' => $salesData];
    }

    private function getInventoryReport($allProducts)
    {
        $inventoryData = [];
        foreach ($allProducts as $p) {
            $inventoryData[] = [
                'Product ID' => 'P-' . str_pad($p->id, 3, '0', STR_PAD_LEFT),
                'Product Name' => $p->name,
                'Category' => $p->category->name ?? 'N/A',
                'SKU' => strtoupper(substr($p->name, 0, 3)) . '-' . str_pad($p->id, 3, '0', STR_PAD_LEFT),
                'Stock Qty' => (string)$p->stock,
                'Reorder Level' => '10',
                'Status' => $p->stock <= 0 ? 'Out of Stock' : ($p->stock < 10 ? 'Low Stock' : 'In Stock')
            ];
        }
        return ['headers' => ['Product ID', 'Product Name', 'Category', 'SKU', 'Stock Qty', 'Reorder Level', 'Status'], 'rows' => $inventoryData];
    }

    private function getCustomerReport($users)
    {
        $customerData = [];
        foreach ($users as $u) {
            $userOrders = collect($u->orders);
            $totalSpent = $userOrders->sum('total');
            $lastOrder = $userOrders->sortByDesc('created_at')->first();
            
            $customerData[] = [
                'Customer ID' => 'C-' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                'Name' => $u->name,
                'Email' => $u->email,
                'Total Orders' => (string)$userOrders->count(),
                'Total Spent' => 'P' . number_format($totalSpent, 2),
                'Last Purchase' => $lastOrder ? $lastOrder->created_at->format('Y-m-d') : 'No Purchase',
                'Status' => $totalSpent > 10000 ? 'VIP' : 'Active'
            ];
        }
        return ['headers' => ['Customer ID', 'Name', 'Email', 'Total Orders', 'Total Spent', 'Last Purchase', 'Status'], 'rows' => $customerData];
    }

    private function getPerformanceReport($orders, $users)
    {
        $totalRev = $orders->sum('total');
        $avgOrder = $orders->count() > 0 ? $totalRev / $orders->count() : 0;
        
        $performanceData = [
            ['Metric' => 'Total Revenue', 'Value' => 'P' . number_format($totalRev, 2), 'Target' => 'P100,000', 'Status' => $totalRev > 50000 ? 'On Track' : 'Behind', 'Period' => 'All Time'],
            ['Metric' => 'Average Order Value', 'Value' => 'P' . number_format($avgOrder, 2), 'Target' => 'P1,500', 'Status' => $avgOrder > 1200 ? 'Good' : 'Low', 'Period' => 'All Time'],
            ['Metric' => 'Total Orders', 'Value' => (string)$orders->count(), 'Target' => '500', 'Status' => $orders->count() > 250 ? 'On Track' : 'Behind', 'Period' => 'All Time'],
            ['Metric' => 'Customer Base', 'Value' => (string)$users->count(), 'Target' => '200', 'Status' => $users->count() > 100 ? 'Good' : 'Growing', 'Period' => 'All Time'],
        ];
        return ['headers' => ['Metric', 'Value', 'Target', 'Status', 'Period'], 'rows' => $performanceData];
    }

    private function getFinancialReport($orders)
    {
        $financialData = [];
        foreach ($orders as $order) {
            $financialData[] = [
                'Transaction ID' => 'TXN-' . (5000 + $order->id),
                'Type' => 'Sale',
                'Amount' => 'P' . number_format($order->total, 2),
                'Payment Method' => 'Cash on Delivery',
                'Status' => $order->status,
                'Date' => $order->created_at->format('Y-m-d')
            ];
        }
        return ['headers' => ['Transaction ID', 'Type', 'Amount', 'Payment Method', 'Status', 'Date'], 'rows' => $financialData];
    }

    private function getReviewsReport()
    {
        $reviews = Review::with(['user', 'product'])->get();
        $reviewsData = [];
        foreach ($reviews as $r) {
            $reviewsData[] = [
                'Review ID' => 'RV-' . str_pad($r->id, 3, '0', STR_PAD_LEFT),
                'Customer' => $r->user->name ?? 'Guest',
                'Product' => $r->product->name ?? 'Deleted Product',
                'Rating' => $r->rating . '/5',
                'Comment' => $r->comment,
                'Date' => $r->created_at->format('Y-m-d'),
                'Status' => 'Published'
            ];
        }
        return ['headers' => ['Review ID', 'Customer', 'Product', 'Rating', 'Comment', 'Date', 'Status'], 'rows' => $reviewsData];
    }
}
