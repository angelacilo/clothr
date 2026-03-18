<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    /**
     * Handle variant arrays/JSON securely
     */
    private function parseVariants($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return is_array($value) ? $value : [];
    }

    private function calculateStock($variantStock, $defaultStock)
    {
        $stockData = $this->parseVariants($variantStock);
        if (!empty($stockData)) {
            return array_sum($stockData);
        }
        return (int) $defaultStock;
    }

    public function create(array $data, $imageFile, array $requestInputs)
    {
        $data['colors'] = $this->parseVariants($requestInputs['variant_colors'] ?? '[]');
        $data['sizes']  = $this->parseVariants($requestInputs['variant_sizes'] ?? '[]');
        $data['stock']  = $this->calculateStock($requestInputs['variant_stock'] ?? '{}', $requestInputs['stock'] ?? 0);

        $data['isFeatured'] = !empty($requestInputs['isFeatured']);
        $data['isOnSale']   = !empty($requestInputs['isOnSale']);
        $data['isNew']      = !empty($requestInputs['isNew']);
        $data['isArchived'] = false;

        if ($imageFile) {
            $path = $imageFile->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        } else {
            $data['images'] = ['/placeholder.png'];
        }

        return Product::create($data);
    }

    public function update(Product $product, array $data, $imageFile, array $requestInputs)
    {
        $data['colors'] = $this->parseVariants($requestInputs['variant_colors'] ?? '[]');
        $data['sizes']  = $this->parseVariants($requestInputs['variant_sizes'] ?? '[]');
        $data['stock']  = $this->calculateStock($requestInputs['variant_stock'] ?? '{}', $requestInputs['stock'] ?? 0);

        $data['isFeatured'] = !empty($requestInputs['isFeatured']);
        $data['isOnSale']   = !empty($requestInputs['isOnSale']);
        $data['isNew']      = !empty($requestInputs['isNew']);
        $data['isArchived'] = !empty($requestInputs['isArchived']);

        if ($imageFile) {
            // Delete old image if it exists and is not a placeholder
            if (!empty($product->images[0]) && $product->images[0] !== '/placeholder.png') {
                $oldPath = str_replace('/storage/', '', $product->images[0]);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $imageFile->store('products', 'public');
            $data['images'] = ['/storage/' . $path];
        }

        $product->update($data);
        return $product;
    }

    public function delete(Product $product)
    {
        if (!empty($product->images[0]) && $product->images[0] !== '/placeholder.png') {
            $oldPath = str_replace('/storage/', '', $product->images[0]);
            Storage::disk('public')->delete($oldPath);
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
