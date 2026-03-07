<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'images']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(12);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        // Auto-generate unique slug from name
        $slug = Str::slug($validated['name']);
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . ($i++);
        }

        // Create product
        $product = Product::create([
            'name'        => $validated['name'],
            'slug'        => $slug,
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'sale_price'  => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'status'      => $validated['status'],
            'is_featured' => $request->has('is_featured'),
        ]);

        // Create inventory record
        Inventory::create([
            'product_id'    => $product->product_id,
            'available_qty' => $validated['stock_quantity'],
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            if (!is_array($images)) {
                $images = [$images];
            }
            foreach ($images as $image) {
                try {
                    $path = $image->store('products', 'public');
                    \Log::info('Image stored at: ' . $path);
                    $record = ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path'    => $path,
                    ]);
                    \Log::info('ProductImage created with id: ' . $record->image_id);
                } catch (\Exception $e) {
                    \Log::error('Image upload error: ' . $e->getMessage());
                }
            }
        } else {
            \Log::info('No images found in request');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $product->load(['category', 'inventory', 'images']);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if (empty($validated['slug'])) {
            $slug = Str::slug($validated['name']);
            $original = $slug;
            $i = 1;
            while (Product::where('slug', $slug)->where('product_id', '!=', $product->product_id)->exists()) {
                $slug = $original . '-' . ($i++);
            }
            $validated['slug'] = $slug;
        }

        $product->update([
            'name'        => $validated['name'],
            'slug'        => $validated['slug'],
            'description' => $validated['description'],
            'price'       => $validated['price'],
            'sale_price'  => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'status'      => $validated['status'],
            'is_featured' => $request->has('is_featured'),
        ]);

        if ($product->inventory) {
            $product->inventory->update(['available_qty' => $validated['stock_quantity']]);
        } else {
            Inventory::create([
                'product_id'    => $product->product_id,
                'available_qty' => $validated['stock_quantity'],
            ]);
        }

        // Handle image uploads
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            if (!is_array($images)) {
                $images = [$images];
            }
            foreach ($images as $image) {
                try {
                    $path = $image->store('products', 'public');
                    \Log::info('Image stored at: ' . $path);
                    $record = ProductImage::create([
                        'product_id' => $product->product_id,
                        'image_path'    => $path,
                    ]);
                    \Log::info('ProductImage updated with id: ' . $record->image_id);
                } catch (\Exception $e) {
                    \Log::error('Image upload error: ' . $e->getMessage());
                }
            }
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        foreach ($product->images as $image) {
            \Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        if ($product->inventory) {
            $product->inventory->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}