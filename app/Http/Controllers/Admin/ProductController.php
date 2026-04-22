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

    public function index(Request $request)
    {
        $query = Product::with('category')->withCount('reviews')->where('isArchived', false);
        
        // Optional status filter
        if ($request->has('status')) {
            if ($request->status === 'low') {
                $query->where('stock', '<=', 5)->where('stock', '>', 0);
            } elseif ($request->status === 'out') {
                $query->where('stock', 0);
            }
        }

        $products   = $query->latest()->paginate(20);
        $categories = Category::all();
        
        // Calculate detailed alerts for ALL variants across catalog
        // We'll build an "Alert Map" to show exactly which color/size is low.
        $allActive = Product::where('isArchived', false)->get(['id', 'name', 'variants', 'stock']);
        $inventoryAlerts = [];
        
        foreach ($allActive as $p) {
            $variants = is_string($p->variants) ? json_decode($p->variants, true) : $p->variants;
            
            if (empty($variants)) {
                if ($p->stock <= 5) {
                    $inventoryAlerts[] = [
                        'id' => $p->id,
                        'name' => $p->name,
                        'color' => 'Default',
                        'size' => 'N/A',
                        'stock' => $p->stock,
                        'type' => $p->stock == 0 ? 'Out of Stock' : 'Low Stock'
                    ];
                }
            } else {
                foreach ($variants as $v) {
                    foreach ($v['sizes'] ?? [] as $sz => $qty) {
                        if ($qty <= 5) {
                            $inventoryAlerts[] = [
                                'id' => $p->id,
                                'name' => $p->name,
                                'color' => $v['color'],
                                'colorHex' => $v['colorHex'] ?? '#ccc',
                                'size' => $sz,
                                'stock' => $qty,
                                'type' => $qty == 0 ? 'Out of Stock' : 'Low Stock'
                            ];
                        }
                    }
                }
            }
        }

        // Sort: Out of stock first, then by quantity
        usort($inventoryAlerts, function($a, $b) {
            return $a['stock'] <=> $b['stock'];
        });

        $totalAlerts = count($inventoryAlerts);
        $limitedAlerts = array_slice($inventoryAlerts, 0, 10);

        return view('admin.products', compact('products', 'categories', 'limitedAlerts', 'totalAlerts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required',
            'price'                => 'required|numeric',
            'category_id'          => 'required',
            'description'          => 'nullable',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'color_images.*'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        // Collect per-color uploaded images keyed by variant index
        $colorImages = [];
        if ($request->hasFile('color_images')) {
            foreach ($request->file('color_images') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $colorImages[$idx] = $file;
                }
            }
        }

        $this->productService->create($validated, $request->file('image'), $request->all(), $colorImages);

        return back()->with('success', 'Product created!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name'                 => 'required',
            'price'                => 'required|numeric',
            'category_id'          => 'required',
            'description'          => 'nullable',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'color_images.*'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $colorImages = [];
        if ($request->hasFile('color_images')) {
            foreach ($request->file('color_images') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $colorImages[$idx] = $file;
                }
            }
        }

        $this->productService->update($product, $validated, $request->file('image'), $request->all(), $colorImages);

        return back()->with('success', 'Product updated!');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->delete($product);
        return back()->with('success', 'Product deleted!');
    }

    public function restock(Request $request, $id)
    {
        $validated = $request->validate([
            'restock' => 'required|array',
        ]);

        $addedTotal = 0;

        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $validated, &$addedTotal) {
            $product = Product::lockForUpdate()->findOrFail($id);
            $variants = is_string($product->variants) ? json_decode($product->variants, true) : $product->variants;

            if (!empty($variants)) {
                $variantUpdated = false;
                foreach ($validated['restock'] as $key => $addQty) {
                    $addQty = (int) $addQty;
                    if ($addQty <= 0) continue;

                    $lastUnderscore = strrpos($key, '_');
                    if ($lastUnderscore === false) continue;
                    
                    $color = substr($key, 0, $lastUnderscore);
                    $size = substr($key, $lastUnderscore + 1);

                    foreach ($variants as &$v) {
                        if ($v['color'] === $color && isset($v['sizes'][$size])) {
                            $v['sizes'][$size] += $addQty;
                            $variantUpdated = true;
                            $addedTotal += $addQty;
                            break;
                        }
                    }
                }

                if ($variantUpdated) {
                    $product->variants = $variants;
                    
                    $totalStock = 0;
                    foreach ($variants as $v) {
                        foreach ($v['sizes'] ?? [] as $qty) {
                            $totalStock += (int) $qty;
                        }
                    }
                    $product->stock = $totalStock;
                    $product->save();
                }

            } else {
                $addQty = (int) ($validated['restock']['default'] ?? 0);
                if ($addQty > 0) {
                    $product->stock += $addQty;
                    $addedTotal += $addQty;
                    $product->save();
                }
            }
        });

        return back()->with('success', "Restock successful! Added {$addedTotal} total units to {$product->name}.");
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
