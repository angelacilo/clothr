<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * STOCK EXPLANATION
     * The variants data structure:
     * [
     *   {
     *     "color": "Blue",
     *     "colorHex": "#0000ff",
     *     "image": "/storage/...",
     *     "sizes": { "S": 10, "M": 5 }
     *   }
     * ]
     * Stock is ALWAYS calculated as the sum of all variant sizes if variants exist.
     * The calcStock() method is the single source of truth for stock calculation.
     * The buildVariants() method structures the form data.
     * The deriveFlat() method extracts flat colors and sizes arrays for cart compatibility.
     */

    /**
     * Safely parse a JSON string or array.
     */
    private function parseVariants($value): array
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
    }

    /**
     * Build the structured variants array from request inputs.
     * Handles per-color image uploads.
     * Returns: [{color, colorHex, image, sizes:{S:n, M:n, ...}}]
     */
    private function buildVariants(array $requestInputs, array $uploadedColorImages = []): array
    {
        $raw = $this->parseVariants($requestInputs['variants_data'] ?? '[]');
        if (empty($raw)) {
            return [];
        }

        $built = [];
        foreach ($raw as $idx => $v) {
            $color    = trim($v['color'] ?? '');
            $colorHex = trim($v['colorHex'] ?? '');
            $sizes    = isset($v['sizes']) && is_array($v['sizes']) ? $v['sizes'] : [];
            $image    = $v['image'] ?? null; // existing stored URL

            // If a new image was uploaded for this color index, use it
            if (isset($uploadedColorImages[$idx])) {
                $path  = $uploadedColorImages[$idx]->store('products', 'public');
                $image = '/storage/' . $path;
            }

            if ($color === '') continue;

            $built[] = [
                'color'    => $color,
                'colorHex' => $colorHex,
                'image'    => $image,
                'sizes'    => $sizes,
            ];
        }
        return $built;
    }

    /**
     * Derive the flat colors[] and sizes[] arrays from structured variants,
     * so legacy code and cart still work.
     */
    private function deriveFlat(array $variants): array
    {
        $colors = [];
        $sizes  = [];
        foreach ($variants as $v) {
            if (!empty($v['color']) && !in_array($v['color'], $colors)) {
                $colors[] = $v['color'];
            }
            foreach (array_keys($v['sizes'] ?? []) as $sz) {
                if (!in_array($sz, $sizes)) {
                    $sizes[] = $sz;
                }
            }
        }
        return compact('colors', 'sizes');
    }

    /**
     * Calculate total stock from variant sizes, or fall back to a plain value.
     */
    private function calcStock(array $variants, $defaultStock): int
    {
        if (!empty($variants)) {
            $total = 0;
            foreach ($variants as $v) {
                foreach ($v['sizes'] ?? [] as $qty) {
                    $total += (int) $qty;
                }
            }
            return $total;
        }
        return (int) $defaultStock;
    }

    /* ──────────────────────────────────────── */

    public function create(array $data, $imageFile, array $requestInputs, array $colorImages = [])
    {
        // 1. Build variants from form
        $variants = $this->buildVariants($requestInputs, $colorImages);
        
        // 2. Derive flat colors and sizes
        $flat     = $this->deriveFlat($variants);

        $data['variants'] = $variants;
        $data['colors']   = $flat['colors'];
        $data['sizes']    = $flat['sizes'];
        
        // 3. Calculate stock from variants
        $data['stock']    = $this->calcStock($variants, $requestInputs['stock'] ?? 0);

        $data['isFeatured'] = !empty($requestInputs['isFeatured']);
        $data['isOnSale']   = !empty($requestInputs['isOnSale']);
        $data['isNew']      = !empty($requestInputs['isNew']);
        $data['isArchived'] = false;

        // Primary product image: use first variant image, or uploaded general image
        if ($imageFile) {
            $path = $imageFile->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        } elseif (!empty($variants[0]['image'])) {
            $data['images'] = [$variants[0]['image']];
        } else {
            $data['images'] = ['/placeholder.png'];
        }

        // 4. Save product
        return Product::create($data);
    }

    public function update(Product $product, array $data, $imageFile, array $requestInputs, array $colorImages = [])
    {
        // 1. Build new variants from form
        $variants = $this->buildVariants($requestInputs, $colorImages);

        // 2. If no new variants provided, keep existing
        if (empty($variants) && !empty($product->variants)) {
            $variants = $product->variants;
        }

        // 3. Derive flat colors and sizes
        $flat = $this->deriveFlat($variants);

        $data['variants'] = $variants;
        $data['colors']   = $flat['colors'];
        $data['sizes']    = $flat['sizes'];
        
        // 4. Calculate stock from variants
        // IMPORTANT: Because variants loaded in the form are the current live ones (including deductions),
        // we can safely recalculate stock here without overwriting live deductions.
        $data['stock']    = $this->calcStock($variants, $requestInputs['stock'] ?? $product->stock);

        $data['isFeatured'] = !empty($requestInputs['isFeatured']);
        $data['isOnSale']   = !empty($requestInputs['isOnSale']);
        $data['isNew']      = !empty($requestInputs['isNew']);
        $data['isArchived'] = !empty($requestInputs['isArchived']);

        if ($imageFile) {
            if (!empty($product->images[0]) && $product->images[0] !== '/placeholder.png') {
                $oldPath = str_replace('/storage/', '', $product->images[0]);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $imageFile->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        } elseif (!empty($variants[0]['image'])) {
            $data['images'] = [$variants[0]['image']];
        }

        // 5. Save product
        $product->update($data);
        return $product;
    }

    public function delete(Product $product)
    {
        if (!empty($product->images[0]) && $product->images[0] !== '/placeholder.png') {
            Storage::disk('public')->delete(str_replace('/storage/', '', $product->images[0]));
        }
        // Clean up variant images
        foreach ($product->variants ?? [] as $v) {
            if (!empty($v['image']) && $v['image'] !== '/placeholder.png') {
                Storage::disk('public')->delete(str_replace('/storage/', '', $v['image']));
            }
        }
        return $product->delete();
    }

    public function archive(Product $product)
    {
        return $product->update(['isArchived' => true]);
    }

    public function restore(Product $product)
    {
        return $product->update(['isArchived' => false]);
    }
}
