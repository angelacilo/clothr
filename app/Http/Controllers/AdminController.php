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
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('customer_info', 'like', "%{$search}%")
                  ->orWhere('tracking_number', 'like', "%{$search}%");
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
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|integer',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data['isFeatured'] = $request->has('isFeatured');
        $data['isOnSale'] = $request->has('isOnSale');
        $data['isNew'] = $request->has('isNew');
        
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
        
        $data = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|integer',
            'description' => 'nullable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        
        $data['isFeatured'] = $request->has('isFeatured');
        $data['isOnSale'] = $request->has('isOnSale');
        $data['isNew'] = $request->has('isNew');
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
        $stats = [
            'total_sales' => Order::sum('total'),
            'orders' => Order::count(),
            'products' => Product::where('isArchived', false)->count(),
            'customers' => User::where('is_admin', false)->count(),
        ];
        $recent_orders = Order::orderBy('created_at', 'desc')->take(10)->get();
        return view('admin.reports', compact('stats', 'recent_orders'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function restoreProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['isArchived' => false]);
        return back()->with('success', 'Product restored!');
    }
}
