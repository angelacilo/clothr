<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;


class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_sales' => Order::sum('total'),
            'orders' => Order::count(),
            'products' => Product::where('isArchived', false)->count(),
            'customers' => User::where('is_admin', false)->count(),
        ];

        // Order status counts
        $statusCounts = [
            'pending' => Order::where('status', 'Pending')->count(),
            'processing' => Order::where('status', 'Processing')->count(),
            'shipped' => Order::where('status', 'Shipped')->count(),
            'delivered' => Order::where('status', 'Delivered')->count(),
            'cancelled' => Order::where('status', 'Cancelled')->count(),
        ];

        // Today's stats
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('total');
        $pendingActions = Order::where('status', 'Pending')->count();
        $processingCount = Order::where('status', 'Processing')->count();

        // Daily sales for the last 8 days
        $dailySales = [];
        $dailyLabels = [];
        for ($i = 7; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels[] = $date->format('M d');
            $dailySales[] = Order::whereDate('created_at', $date)->sum('total');
        }

        // Revenue by category
        $categories = Category::where('isVisible', true)->get();
        $categoryLabels = [];
        $categoryRevenue = [];
        foreach ($categories as $cat) {
            $categoryLabels[] = $cat->name;
            $productIds = Product::where('category_id', $cat->id)->pluck('id')->toArray();
            // Sum order totals that contain products in this category
            $revenue = 0;
            $orders = Order::all();
            foreach ($orders as $order) {
                foreach ($order->items as $item) {
                    if (in_array($item['id'] ?? 0, $productIds)) {
                        $revenue += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                    }
                }
            }
            $categoryRevenue[] = $revenue;
        }

        // Low stock count
        $lowStockCount = Product::where('isArchived', false)->where('stock', '<', 10)->count();

        $recent_orders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'stats', 'statusCounts', 'todayOrders', 'todayRevenue',
            'pendingActions', 'processingCount', 'dailySales', 'dailyLabels',
            'categoryLabels', 'categoryRevenue', 'lowStockCount', 'recent_orders'
        ));
    }

    public function orders(Request $request)
    {
        $query = Order::with('user')->orderBy('created_at', 'desc');

        // Filter by status
        $statusFilter = $request->get('status', 'all');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Search
        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                // If search is a number or starts with #
                $cleanId = str_replace('#', '', $search);
                if (is_numeric($cleanId)) {
                    $rawId = (int)$cleanId > 1000 ? (int)$cleanId - 1000 : (int)$cleanId;
                    $q->where('id', $rawId);
                } else {
                    $q->where('customer_info', 'like', "%{$search}%")
                      ->orWhere('tracking_number', 'like', "%{$search}%");
                }
            });
        }

        $orders = $query->get();
        return view('admin.orders', compact('orders', 'statusFilter'));
    }

    public function orderDetails($id)
    {
        $order = Order::with('user')->findOrFail($id);
        return response()->json($order);
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $request->validate(['status' => 'required|in:Pending,Processing,Shipped,Delivered,Cancelled']);

        $newStatus = $request->status;
        $currentStatus = $order->status;

        // Prevent invalid status jumps
        $validTransitions = [
            'Pending'    => ['Processing', 'Cancelled'],
            'Processing' => ['Pending', 'Shipped', 'Cancelled'],
            'Shipped'    => ['Processing', 'Delivered', 'Cancelled'],
            'Delivered'  => ['Shipped'], // allow rollback for mistakes
            'Cancelled'  => ['Pending'], // allow restoring from cancelled
        ];

        if ($newStatus !== $currentStatus) {
            $allowed = $validTransitions[$currentStatus] ?? [];
            if (!in_array($newStatus, $allowed)) {
                return back()->with('error', "Invalid jump: Cannot change from {$currentStatus} directly to {$newStatus}.");
            }
        }

        $data = ['status' => $newStatus];

        // Auto-set timestamps based on status
        switch ($request->status) {
            case 'Processing':
                $data['processing_at'] = now();
                break;
            case 'Shipped':
                if (!$order->processing_at) $data['processing_at'] = now();
                $data['shipped_at'] = now();
                break;
            case 'Delivered':
                if (!$order->processing_at) $data['processing_at'] = now();
                if (!$order->shipped_at) $data['shipped_at'] = now();
                $data['delivered_at'] = now();
                break;
            case 'Cancelled':
                $data['cancelled_at'] = now();
                break;
            case 'Pending':
                // Reset all timestamps if reverting to Pending
                $data['processing_at'] = null;
                $data['shipped_at'] = null;
                $data['delivered_at'] = null;
                $data['cancelled_at'] = null;
                break;
        }

        $order->update($data);
        return back()->with('success', 'Order status updated to ' . $request->status);
    }

    public function updateOrderCourier(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'courier_name' => 'nullable|string|max:100',
            'tracking_number' => 'nullable|string|max:100',
        ]);
        $order->update($data);
        return back()->with('success', 'Courier information updated');
    }

    public function products()
    {
        $products = Product::with('category')->where('isArchived', false)->get();
        $categories = Category::all();
        return view('admin.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'category_id' => 'required',
            'description' => 'nullable',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'category_id', 'description']);

        // Handle Colors & Sizes (accept JSON string or plain array)
        $colors = $request->input('variant_colors', '[]');
        $sizes  = $request->input('variant_sizes',  '[]');
        $data['colors'] = is_string($colors) ? (json_decode($colors, true) ?: []) : $colors;
        $data['sizes']  = is_string($sizes)  ? (json_decode($sizes, true)  ?: []) : $sizes;

        // Stock Calculation
        $variantStock = $request->input('variant_stock', '{}');
        $stockData    = is_string($variantStock) ? (json_decode($variantStock, true) ?: []) : $variantStock;
        
        if (!empty($stockData)) {
            $data['stock'] = array_sum($stockData);
        } else {
            $request->validate(['stock' => 'required|integer']);
            $data['stock'] = $request->input('stock', 0);
        }

        $data['isFeatured'] = $request->has('isFeatured');
        $data['isOnSale']   = $request->has('isOnSale');
        $data['isNew']      = $request->has('isNew');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        } else {
            $data['images'] = ['/placeholder.png'];
        }

        Product::create($data);
        return back()->with('success', 'Product created!');
    }


    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'category_id' => 'required',
            'stock'       => 'required|integer',
            'description' => 'nullable',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'price', 'category_id', 'stock', 'description']);

        // Handle Colors & Sizes (accept JSON string or plain array)
        $colors = $request->input('variant_colors', '[]');
        $sizes  = $request->input('variant_sizes',  '[]');
        $data['colors'] = is_string($colors) ? (json_decode($colors, true) ?: []) : $colors;
        $data['sizes']  = is_string($sizes)  ? (json_decode($sizes, true)  ?: []) : $sizes;

        $data['isFeatured'] = $request->has('isFeatured');
        $data['isOnSale']   = $request->has('isOnSale');
        $data['isNew']      = $request->has('isNew');
        $data['isArchived'] = $request->has('isArchived');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        }

        $product->update($data);
        return back()->with('success', 'Product updated!');
    }


    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return back()->with('success', 'Product deleted!');
    }

    public function archiveProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['isArchived' => true]);
        return back()->with('success', 'Product archived!');
    }

    public function categories()
    {
        $categories = Category::all();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required']);
        $slug = \Illuminate\Support\Str::slug($request->name);
        Category::create(['name' => $request->name, 'slug' => $slug]);
        return back()->with('success', 'Category created!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $slug = \Illuminate\Support\Str::slug($request->name);
        $category->update(['name' => $request->name, 'slug' => $slug, 'isVisible' => $request->has('isVisible')]);
        return back()->with('success', 'Category updated!');
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return back()->with('success', 'Category deleted!');
    }

    public function archive()
    {
        $archived = Product::where('isArchived', true)->get();
        return view('admin.archive', compact('archived'));
    }

    public function users()
    {
        $users = User::with('orders')->where('is_admin', false)->get();
        return view('admin.users', compact('users'));
    }

    public function reviews()
    {
        return view('admin.reviews');
    }

    public function reports()
    {
        // 1. Sales Report Data
        $orders = Order::all();
        $productsMap = Product::with('category')->get()->keyBy('id');
        
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

        // 2. Inventory Report Data
        $allProducts = Product::with('category')->get();
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

        // 3. Customer Report Data
        $users = User::where('is_admin', false)->with('orders')->get();
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

        // 4. Performance Report Data
        $totalRev = $orders->sum('total');
        $avgOrder = $orders->count() > 0 ? $totalRev / $orders->count() : 0;
        $performanceData = [
            ['Metric' => 'Total Revenue', 'Value' => 'P' . number_format($totalRev, 2), 'Target' => 'P100,000', 'Status' => $totalRev > 50000 ? 'On Track' : 'Behind', 'Period' => 'All Time'],
            ['Metric' => 'Average Order Value', 'Value' => 'P' . number_format($avgOrder, 2), 'Target' => 'P1,500', 'Status' => $avgOrder > 1200 ? 'Good' : 'Low', 'Period' => 'All Time'],
            ['Metric' => 'Total Orders', 'Value' => (string)$orders->count(), 'Target' => '500', 'Status' => $orders->count() > 250 ? 'On Track' : 'Behind', 'Period' => 'All Time'],
            ['Metric' => 'Customer Base', 'Value' => (string)$users->count(), 'Target' => '200', 'Status' => $users->count() > 100 ? 'Good' : 'Growing', 'Period' => 'All Time'],
        ];

        // 5. Financial Report Data
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

        // 6. Reviews Report Data
        $reviews = \App\Models\Review::with(['user', 'product'])->get();
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

        $allData = [
            'Sales Report' => ['headers' => ['Order ID', 'Product', 'Category', 'Quantity', 'Unit Price', 'Total', 'Date'], 'rows' => $salesData],
            'Inventory Report' => ['headers' => ['Product ID', 'Product Name', 'Category', 'SKU', 'Stock Qty', 'Reorder Level', 'Status'], 'rows' => $inventoryData],
            'Customer Report' => ['headers' => ['Customer ID', 'Name', 'Email', 'Total Orders', 'Total Spent', 'Last Purchase', 'Status'], 'rows' => $customerData],
            'Performance Report' => ['headers' => ['Metric', 'Value', 'Target', 'Status', 'Period'], 'rows' => $performanceData],
            'Financial Report' => ['headers' => ['Transaction ID', 'Type', 'Amount', 'Payment Method', 'Status', 'Date'], 'rows' => $financialData],
            'Reviews Report' => ['headers' => ['Review ID', 'Customer', 'Product', 'Rating', 'Comment', 'Date', 'Status'], 'rows' => $reviewsData],
        ];

        return view('admin.reports', compact('allData'));
    }

    public function settings()
    {
        $admin = auth()->user();
        return view('admin.settings', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->user();
        if (!$admin) return back()->with('error', 'Unauthorized');

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = $request->only(['name', 'email', 'phone', 'bio']);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = '/storage/' . $path;
        }

        $admin->update($data);
        return back()->with('success', 'Profile updated successfully');
    }

    public function restoreProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['isArchived' => false]);
        return back()->with('success', 'Product restored!');
    }
}
