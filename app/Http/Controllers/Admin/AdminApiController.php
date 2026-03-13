<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminApiController extends Controller
{
    public function stats()
    {
        return (new AdminDashboardController())->apiStats();
    }

    public function products(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'images']);
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        return $query->latest()->paginate(12);
    }

    public function product($id)
    {
        return Product::with(['category', 'inventory', 'images'])->findOrFail($id);
    }

    public function categories()
    {
        return Category::withCount('products')->get();
    }

    public function orders(Request $request)
    {
        $query = Order::with(['user']);
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        return $query->latest()->paginate(15);
    }

    public function order($id)
    {
        return Order::with(['user', 'items.product'])->findOrFail($id);
    }

    public function users(Request $request)
    {
        $query = User::query();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        return $query->latest()->paginate(15);
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete admin'], 422);
        }
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function reviews(Request $request)
    {
        $query = Review::with(['user', 'product'])->latest('created_at');
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }
        return $query->paginate(15);
    }

    public function reportSummary(Request $request)
    {
        $range = $request->get('range', 'all');
        $query = Order::query();
        if ($range === 'today') {
            $query->whereDate('order_date', today());
        } elseif ($range === 'week') {
            $query->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'month') {
            $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
        } elseif ($range === 'year') {
            $query->whereYear('order_date', now()->year);
        }
        $orders = (clone $query)->whereNotIn('order_status', ['cancelled'])->get();
        $lowStock = Inventory::with('product.category')->where('available_qty', '<=', 5)->get()
            ->map(fn ($i) => [
                'product_id'    => $i->product_id,
                'name'          => $i->product->name ?? '-',
                'category_name' => $i->product->category->category_name ?? '-',
                'available_qty' => $i->available_qty,
            ]);
        return response()->json([
            'total_revenue'   => $orders->sum('total_amount'),
            'total_orders'    => $orders->count(),
            'avg_order_value' => $orders->count() ? $orders->avg('total_amount') : 0,
            'total_discounts' => $orders->sum('discount_amount'),
            'low_stock'       => $lowStock,
        ]);
    }

    public function reportTopProducts(Request $request)
    {
        $range = $request->get('range', 'all');
        $query = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.order_id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.category_id')
            ->whereNotIn('orders.order_status', ['cancelled'])
            ->select(
                'products.product_id',
                'products.name',
                'categories.category_name',
                DB::raw('SUM(order_items.quantity) as total_sold'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_revenue')
            );
        if ($range === 'today') {
            $query->whereDate('orders.order_date', today());
        } elseif ($range === 'week') {
            $query->whereBetween('orders.order_date', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($range === 'month') {
            $query->whereMonth('orders.order_date', now()->month)->whereYear('orders.order_date', now()->year);
        } elseif ($range === 'year') {
            $query->whereYear('orders.order_date', now()->year);
        }
        return $query->groupBy('products.product_id', 'products.name', 'categories.category_name')
            ->orderByDesc('total_sold')->limit(10)->get();
    }

    public function reportOrdersByStatus()
    {
        return DB::table('orders')->select('order_status', DB::raw('COUNT(*) as count'))->groupBy('order_status')->get();
    }
}
