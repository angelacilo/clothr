<?php

/**
 * FILE: ProductService.php
 * WHAT IT DOES: This is the "Engine" or "Logic Helper" for products.
 * WHY: We keep complex code here so the Controller stays clean and easy to read.
 * HOW IT WORKS: It handles the "Dirty Work" like calculating stock totals, uploading images, and formatting the color/size data.
 */

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * STOCK EXPLANATION (For the Panelist)
     * 
     * How do we store Colors and Sizes?
     * We use a "JSON" structure inside the database. It looks like a list of objects:
     * [
     *   {
     *     "color": "Blue",
     *     "colorHex": "#0000ff",
     *     "image": "/storage/...",
     *     "sizes": { "S": 10, "M": 5 }
     *   }
     * ]
     * 
     * Why? 
     * This allows one product to have many colors and many sizes without needing 10 different tables.
     * 
     * How do we calculate Total Stock?
     * We sum up the numbers in the "sizes" section for every color.
     */

    /**
     * WHAT IT DOES: Converts raw text (JSON) into a readable list (Array).
     */
    private function parseVariants($value): array
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
    }

    /**
     * WHAT IT DOES: Takes the data from the "Add/Edit" form and organizes it for the database.
     * HOW: It loops through the colors, checks for uploaded images, and saves the file paths.
     */
    private function buildVariants(array $requestInputs, array $uploadedColorImages = []): array
    {
        $raw = $this->parseVariants($requestInputs['variants_data'] ?? '[]');
        if (empty($raw)) {
            return [];
        }

        $built = [];
        $colorMap = [];

        foreach ($raw as $idx => $v) {
            $color    = trim($v['color'] ?? '');
            $colorHex = trim($v['colorHex'] ?? '');
            $sizes    = isset($v['sizes']) && is_array($v['sizes']) ? $v['sizes'] : [];
            $image    = $v['image'] ?? null;

            if ($color === '') continue;

            // IMAGE UPLOAD: If a new photo was chosen for this color, save it.
            if (isset($uploadedColorImages[$idx])) {
                $path  = $uploadedColorImages[$idx]->store('products', 'public');
                $image = '/storage/' . $path;
            }
            
            // SECURITY: Sanitize each size key and stock quantity.
            $cleanSizes = [];
            foreach ($sizes as $sizeKey => $qty) {
                $cleanKey = substr(trim((string)$sizeKey), 0, 20);
                if ($cleanKey === '') continue;
                $cleanQty = max(0, (int)$qty);
                $cleanSizes[$cleanKey] = $cleanQty;
            }

            // DUPLICATE PROTECTION: If this color already exists in this product, merge the sizes.
            if (isset($colorMap[$color])) {
                $existingIdx = $colorMap[$color];
                foreach ($cleanSizes as $sz => $qty) {
                    if (isset($built[$existingIdx]['sizes'][$sz])) {
                        $built[$existingIdx]['sizes'][$sz] += $qty;
                    } else {
                        $built[$existingIdx]['sizes'][$sz] = $qty;
                    }
                }
                // If the duplicate entry has an image and the existing one doesn't, take it.
                if ($image && !$built[$existingIdx]['image']) {
                    $built[$existingIdx]['image'] = $image;
                }
            } else {
                $colorMap[$color] = count($built);
                $built[] = [
                    'color'    => $color,
                    'colorHex' => $colorHex,
                    'image'    => $image,
                    'sizes'    => $cleanSizes,
                ];
            }
        }
        return $built;
    }

    /**
     * WHAT IT DOES: Extracts a simple list of colors (e.g., ["Red", "Blue"]) and sizes (["S", "M", "L"]).
     * WHY: So the customer-facing website can easily show the dropdown filters.
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
     * WHAT IT DOES: The "Math" part of stock management.
     * HOW: It iterates through every color and size and adds up the quantities.
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

    /**
     * WHAT IT DOES: Creates a brand new product.
     * STEP 1: Process images.
     * STEP 2: Calculate stock.
     * STEP 3: Save to database.
     */
    public function create(array $data, $imageFile, array $requestInputs, array $colorImages = [])
    {
        // Organize the color/size data.
        $variants = $this->buildVariants($requestInputs, $colorImages);
        $flat     = $this->deriveFlat($variants);

        $data['variants'] = $variants;
        $data['colors']   = $flat['colors'];
        $data['sizes']    = $flat['sizes'];
        
        // Use our math function to get the total stock number.
        $data['stock']    = $this->calcStock($variants, $requestInputs['stock'] ?? 0);

        // Set badges (New, On Sale, Featured).
        $data['isFeatured'] = !empty($requestInputs['isFeatured']);
        $data['isOnSale']   = !empty($requestInputs['isOnSale']);
        $data['isNew']      = !empty($requestInputs['isNew']);
        $data['isArchived'] = false;

        // MAIN IMAGE: If no main photo was uploaded, use the first color photo as the thumbnail.
        if ($imageFile) {
            $path = $imageFile->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        } elseif (!empty($variants[0]['image'])) {
            $data['images'] = [$variants[0]['image']];
        } else {
            $data['images'] = ['/placeholder.png'];
        }

        return Product::create($data);
    }

    /**
     * WHAT IT DOES: Updates an existing product.
     */
    public function update(Product $product, array $data, $imageFile, array $requestInputs, array $colorImages = [])
    {
        $variants = $this->buildVariants($requestInputs, $colorImages);

        if (empty($variants) && !empty($product->variants)) {
            $variants = $product->variants;
        }

        $flat = $this->deriveFlat($variants);

        $data['variants'] = $variants;
        $data['colors']   = $flat['colors'];
        $data['sizes']    = $flat['sizes'];
        
        $data['stock']    = $this->calcStock($variants, $requestInputs['stock'] ?? $product->stock);

        $data['isFeatured'] = !empty($requestInputs['isFeatured']);
        $data['isOnSale']   = !empty($requestInputs['isOnSale']);
        $data['isNew']      = !empty($requestInputs['isNew']);
        $data['isArchived'] = !empty($requestInputs['isArchived']);

        // If a new main image is uploaded, we DELETE the old file to save space on the server.
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

        $product->update($data);
        return $product;
    }

    /**
     * WHAT IT DOES: Deletes a product and its images from the server storage.
     */
    public function delete(Product $product)
    {
        if (!empty($product->images[0]) && $product->images[0] !== '/placeholder.png') {
            $path = str_replace('/storage/', '', $product->images[0]);
            Storage::disk('public')->delete($path);
        }
        return $product->delete();
    }

    /**
     * WHAT IT DOES: Hides a product from the website (Soft Delete).
     */
    public function archive(Product $product)
    {
        $product->isArchived = true;
        return $product->save();
    }

    /**
     * WHAT IT DOES: Shows a hidden product again.
     */
    public function restore(Product $product)
    {
        $product->isArchived = false;
        return $product->save();
    }
}
