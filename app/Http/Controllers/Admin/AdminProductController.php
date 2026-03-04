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
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'is_featured' => $request->has('is_featured'),
        ]);

        // Create inventory record
        Inventory::create([
            'product_id' => $product->product_id,
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
                        'img_url' => $path,
                    ]);
                    \Log::info('ProductImage created with id: ' . $record->image_id);
                }
                catch (\Exception $e) {
                    \Log::error('Image upload error: ' . $e->getMessage());
                }
            }
        }
        else {
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
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'is_featured' => $request->has('is_featured'),
        ]);

        if ($product->inventory) {
            $product->inventory->update(['available_qty' => $validated['stock_quantity']]);
        }
        else {
            Inventory::create([
                'product_id' => $product->product_id,
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
                        'img_url' => $path,
                    ]);
                    \Log::info('ProductImage updated with id: ' . $record->image_id);
                }
                catch (\Exception $e) {
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
            \Storage::disk('public')->delete($image->img_url);
            $image->delete();
        }

        if ($product->inventory) {
            $product->inventory->delete();
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    // --- API Methods ---------------------------------------------------------

    /** GET /api/admin/products */
    public function apiIndex(Request $request)
    {
        $query = Product::with(['category', 'inventory', 'images']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(12));
    }

    /** GET /api/admin/products/{product} */
    public function apiShow(Product $product)
    {
        $product->load(['category', 'inventory', 'images', 'reviews']);
        return response()->json($product);
    }

    /** POST /api/admin/products */
    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,category_id',
            'status' => 'required|in:active,inactive',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        $slug = Str::slug($validated['name']);
        $original = $slug;
        $i = 1;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $original . '-' . ($i++);
        }

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'sale_price' => $validated['sale_price'] ?? null,
            'category_id' => $validated['category_id'],
            'status' => $validated['status'],
            'is_featured' => $request->boolean('is_featured'),
        ]);

        Inventory::create([
            'product_id' => $product->product_id,
            'available_qty' => $validated['stock_quantity'],
        ]);

        $product->load(['category', 'inventory', 'images']);
        return response()->json($product, 201);
    }

    /** PUT /api/admin/products/{product} */
    public function apiUpdate(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'sometimes|exists:categories,category_id',
            'status' => 'sometimes|in:active,inactive',
            'stock_quantity' => 'sometimes|integer|min:0',
        ]);

        if (isset($validated['name'])) {
            $slug = Str::slug($validated['name']);
            $original = $slug;
            $i = 1;
            while (Product::where('slug', $slug)->where('product_id', '!=', $product->product_id)->exists()) {
                $slug = $original . '-' . ($i++);
            }
            $validated['slug'] = $slug;
        }

        if (array_key_exists('is_featured', $request->all())) {
            $validated['is_featured'] = $request->boolean('is_featured');
        }

        $product->update($validated);

        if (isset($validated['stock_quantity'])) {
            if ($product->inventory) {
                $product->inventory->update(['available_qty' => $validated['stock_quantity']]);
            }
            else {
                Inventory::create(['product_id' => $product->product_id, 'available_qty' => $validated['stock_quantity']]);
            }
        }

        $product->load(['category', 'inventory', 'images']);
        return response()->json($product);
    }

    /** DELETE /api/admin/products/{product} */
    public function apiDestroy(Product $product)
    {
        foreach ($product->images as $image) {
            \Storage::disk('public')->delete($image->img_url);
            $image->delete();
        }
        if ($product->inventory) {
            $product->inventory->delete();
        }
        $product->delete();

        return response()->json(['success' => true]);
    }
}