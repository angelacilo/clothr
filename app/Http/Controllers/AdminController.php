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
            'products' => Product::count(),
            'customers' => User::count(),
        ];
        $recent_orders = Order::orderBy('created_at', 'desc')->take(5)->get();
        return view('admin.dashboard', compact('stats', 'recent_orders'));
    }

    public function orders()
    {
        $orders = Order::orderBy('created_at', 'desc')->get();
        return view('admin.orders', compact('orders'));
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
        ]);

        $data['isFeatured'] = $request->has('isFeatured');
        $data['isOnSale'] = $request->has('isOnSale');
        $data['isNew'] = $request->has('isNew');
        $data['images'] = ['/placeholder.png']; // Default image for now

        Product::create($data);
        return back()->with('success', 'Product created!');
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->all();
        
        $data['isFeatured'] = $request->has('isFeatured');
        $data['isOnSale'] = $request->has('isOnSale');
        $data['isNew'] = $request->has('isNew');
        $data['isArchived'] = $request->has('isArchived');

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
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function reviews()
    {
        return view('admin.reviews');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function settings()
    {
        return view('admin.settings');
    }
}
