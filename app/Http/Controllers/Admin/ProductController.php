<?php

/**
 * FILE: ProductController.php
 * WHAT IT DOES: This is the "Brain" for managing products in the Admin panel.
 * WHY: It handles listing, adding, editing, deleting, and restocking products.
 * HOW IT WORKS: It communicates with the Database (Product Model) and the ProductService (Logic Helper).
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ProductService;

class ProductController extends Controller
{
    // This variable holds our "Helper Service" so we can use it anywhere in this file.
    protected $productService;

    // The "Constructor" runs automatically when the controller starts. 
    // It grabs the ProductService so we can use its logic.
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * WHAT IT DOES: Shows the list of all products (The Catalog).
     * WHY: So the admin can see what is in stock and what is running low.
     * HOW: It fetches products from the database, filters them if needed, and builds "Alerts" for low stock.
     */
    public function index(Request $request)
    {
        // 1. Prepare a "Query" to find products that are not archived.
        $query = Product::with('category')->withCount('reviews')->where('isArchived', false);
        
        // 2. If the admin clicked "Low Stock" or "Out of Stock" filters, we change the query.
        if ($request->has('status')) {
            if ($request->status === 'low') {
                $query->where('stock', '<=', 5)->where('stock', '>', 0);
            } elseif ($request->status === 'out') {
                $query->where('stock', 0);
            }
        }

        // 3. Get the products and split them into pages (20 per page).
        $products   = $query->latest()->paginate(20);
        $categories = Category::all();
        
        // 4. INVENTORY ALERTS: We look through all products to find specific colors/sizes that are low.
        $allActive = Product::where('isArchived', false)->get(['id', 'name', 'variants', 'stock']);
        $inventoryAlerts = [];
        
        foreach ($allActive as $p) {
            // Check if the product has variations (colors/sizes)
            $variants = is_string($p->variants) ? json_decode($p->variants, true) : $p->variants;
            
            if (empty($variants)) {
                // If no variants, just check the main stock number.
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
                // If it has variants, we check every single color and size combo.
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

        // Sort the alerts so the most critical (Out of Stock) appear at the top.
        usort($inventoryAlerts, function($a, $b) {
            return $a['stock'] <=> $b['stock'];
        });

        $totalAlerts = count($inventoryAlerts);
        $limitedAlerts = array_slice($inventoryAlerts, 0, 10);

        // Send all this data to the "products.blade.php" file to display it.
        return view('admin.products', compact('products', 'categories', 'limitedAlerts', 'totalAlerts'));
    }

    /**
     * WHAT IT DOES: Saves a brand new product to the database.
     * WHY: To add new items to the store.
     */
    public function store(Request $request)
    {
        // 1. VALIDATION: Make sure the admin didn't leave important fields empty.
        $validated = $request->validate([
            'name'                 => 'required',
            'price'                => 'required|numeric',
            'category_id'          => 'required',
            'description'          => 'nullable',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'color_images.*'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'variants_data'        => 'nullable|string',
        ]);

        // 2. Handle variant images (if the admin uploaded photos for specific colors).
        $colorImages = [];
        if ($request->hasFile('color_images')) {
            foreach ($request->file('color_images') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $colorImages[$idx] = $file;
                }
            }
        }

        // 3. Use the "Service" to do the heavy lifting of saving the product.
        $this->productService->create($validated, $request->file('image'), $request->all(), $colorImages);

        // 4. Go back to the previous page with a "Success" message.
        return back()->with('success', 'Product created!');
    }

    /**
     * WHAT IT DOES: Updates an existing product's information.
     */
    public function update(Request $request, $id)
    {
        // 1. Find the product or fail if it doesn't exist.
        $product = Product::findOrFail($id);

        // 2. Check the inputs.
        $validated = $request->validate([
            'name'                 => 'required',
            'price'                => 'required|numeric',
            'category_id'          => 'required',
            'description'          => 'nullable',
            'image'                => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'color_images.*'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'variants_data'        => 'nullable|string',
        ]);

        $colorImages = [];
        if ($request->hasFile('color_images')) {
            foreach ($request->file('color_images') as $idx => $file) {
                if ($file && $file->isValid()) {
                    $colorImages[$idx] = $file;
                }
            }
        }

        // 3. Update the product using the Service.
        $this->productService->update($product, $validated, $request->file('image'), $request->all(), $colorImages);

        return back()->with('success', 'Product updated!');
    }

    /**
     * WHAT IT DOES: Completely deletes a product from the database.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->delete($product);
        return back()->with('success', 'Product deleted!');
    }

    /**
     * WHAT IT DOES: Fetches only the stock data (colors/sizes) for a product.
     * WHY: This is used by the "Restock Modal" via AJAX (background request).
     * HOW: It returns a "JSON" response (data format for JavaScript) instead of a full webpage.
     */
    public function stockData($id)
    {
        $product  = Product::findOrFail($id);
        $variants = $product->variants ?? [];
        
        if (!empty($variants)) {
            return response()->json([
                'hasVariants' => true,
                'variants'    => collect($variants)
                    ->map(fn($v) => [
                        'color'    => $v['color'],
                        'colorHex' => $v['colorHex'] ?? '#ccc',
                        'sizes'    => $v['sizes'] ?? [],
                    ])
                    ->values(),
            ]);
        }
        
        return response()->json([
            'hasVariants' => false,
            'stock'       => (int) $product->stock,
        ]);
    }

    /**
     * WHAT IT DOES: Adds more stock to a product.
     * WHY: When new shipments arrive, the admin uses this to update inventory.
     * HOW: It uses a "Transaction" to make sure the update is safe and no data is lost.
     */
    public function restock(Request $request, $id)
    {
        // 1. VALIDATION: Ensure numbers are whole integers and not negative.
        $request->validate([
            'restock'   => 'required|array',
            'restock.*' => 'integer|min:0',
        ]);
        
        $product = Product::findOrFail($id);
        $restock = $request->input('restock', []);
        $added   = 0;
        
        // 2. DATABASE TRANSACTION: This "locks" the record while we update it.
        // If two people try to restock at the same exact microsecond, one will wait for the other.
        \Illuminate\Support\Facades\DB::transaction(function () use ($product, $restock, &$added) {
            
            // "lockForUpdate" prevents other parts of the app from changing this product while we are working.
            $locked = Product::lockForUpdate()->find($product->id);
            
            $variants = $locked->variants;
            
            if (!empty($variants)) {
                
                // We loop through the inputs from the form.
                foreach ($restock as $key => $qty) {
                    $qty = (int) $qty;
                    if ($qty <= 0) continue;
                    
                    // The key is formatted as "ColorName_SizeName" (e.g., "Blue_M").
                    $underscorePos = strpos($key, '_');
                    if ($underscorePos === false) continue;
                    
                    $color = substr($key, 0, $underscorePos);
                    $size  = substr($key, $underscorePos + 1);
                    
                    // Find the matching color and size in the database and ADD the units.
                    foreach ($variants as &$v) {
                        if ($v['color'] === $color) {
                            if (array_key_exists($size, $v['sizes'])) {
                                $v['sizes'][$size] += $qty;
                                $added += $qty;
                            }
                            break;
                        }
                    }
                    unset($v); // Clean up the reference.
                }
                
                // 3. RECALCULATE TOTAL: We sum up every single size to get the new total stock number.
                $total = 0;
                foreach ($variants as $v) {
                    foreach ($v['sizes'] ?? [] as $qty) {
                        $total += (int) $qty;
                    }
                }
                
                $locked->variants = $variants;
                $locked->stock    = $total;
                $locked->save();
                
            } else {
                // If it's a simple product with no colors/sizes, just add to the main stock.
                $qty = (int) ($restock['default'] ?? 0);
                if ($qty > 0) {
                    $locked->stock += $qty;
                    $locked->save();
                    $added = $qty;
                }
            }
        });
        
        // 4. FEEDBACK: Tell the admin exactly how many units were added.
        $message = $added > 0
            ? "Restock successful! Added {$added} units to {$product->name}."
            : 'No changes made. All quantities were 0.';
        
        return back()->with('success', $message);
    }

    /**
     * WHAT IT DOES: Removes a specific color/size variation from a product forever.
     * WHY: If the admin doesn't sell a certain size anymore, they can clean it up.
     */
    public function removeSize(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $color = $request->input('color');
        $size = $request->input('size');
        
        $variants = $product->variants;
        $found = false;
        
        foreach ($variants as &$v) {
            if ($v['color'] === $color) {
                if (isset($v['sizes'][$size])) {
                    unset($v['sizes'][$size]);
                    $found = true;
                }
                break;
            }
        }
        
        if ($found) {
            // Recalculate total stock after removal
            $total = 0;
            foreach ($variants as $v) {
                foreach ($v['sizes'] ?? [] as $qty) {
                    $total += (int) $qty;
                }
            }
            $product->variants = $variants;
            $product->stock = $total;
            $product->save();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Variation not found.']);
    }

    /**
     * WHAT IT DOES: Moves a product to the Archive.
     * WHY: Instead of deleting, we archive so we can restore it later if needed.
     */
    public function archive($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->archive($product);
        return back()->with('success', 'Product archived!');
    }

    /**
     * WHAT IT DOES: Brings a product back from the Archive.
     */
    public function restore($id)
    {
        $product = Product::findOrFail($id);
        $this->productService->restore($product);
        return back()->with('success', 'Product restored!');
    }

    /**
     * WHAT IT DOES: Shows the list of all archived products.
     */
    public function archivedIndex()
    {
        $archived = Product::where('isArchived', true)->latest()->paginate(20);
        return view('admin.archive', compact('archived'));
    }
}
