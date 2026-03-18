<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = Product::with('category')->where('isArchived', false)->latest()->paginate(20);
        $categories = Category::all();
        return view('admin.products', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'category_id' => 'required',
            'description' => 'nullable',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $this->productService->create($validated, $request->file('image'), $request->all());
        
        return back()->with('success', 'Product created!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'        => 'required',
            'price'       => 'required|numeric',
            'category_id' => 'required',
            'description' => 'nullable',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $this->productService->update($product, $validated, $request->file('image'), $request->all());

        return back()->with('success', 'Product updated!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->delete($product);
        return back()->with('success', 'Product deleted!');
    }

    public function archive($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->archive($product);
        return back()->with('success', 'Product archived!');
    }

    public function restore($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->restore($product);
        return back()->with('success', 'Product restored!');
    }

    public function archivedIndex()
    {
        $archived = Product::where('isArchived', true)->latest()->paginate(20);
        return view('admin.archive', compact('archived'));
    }
}
