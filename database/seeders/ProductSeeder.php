<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dresses = \App\Models\Category::create(['name' => 'Dresses', 'slug' => 'dresses']);
        $tops = \App\Models\Category::create(['name' => 'Tops & Blouses', 'slug' => 'tops-blouses']);
        $bottoms = \App\Models\Category::create(['name' => 'Bottoms', 'slug' => 'bottoms']);
        $outerwear = \App\Models\Category::create(['name' => 'Outerwear', 'slug' => 'outerwear']);
        $accessories = \App\Models\Category::create(['name' => 'Accessories', 'slug' => 'accessories']);

        \App\Models\Product::create([
            'name' => 'Floral Summer Dress',
            'description' => 'Beautiful floral print dress perfect for summer days.',
            'price' => 1299.00,
            'category_id' => $dresses->id,
            'images' => ['/images/products/floral_summer_dress.png'],
            'stock' => 15,
            'isNew' => true,
            'isFeatured' => true,
            'sizes' => ['XS', 'S', 'M', 'L', 'XL']
        ]);

        \App\Models\Product::create([
            'name' => 'Chic Midi Dress',
            'description' => 'Elegant beige midi dress with a professional blazer-style top.',
            'price' => 2499.00,
            'category_id' => $dresses->id,
            'images' => ['/images/products/chic_midi_dress.png'],
            'stock' => 10,
            'isFeatured' => true,
            'sizes' => ['S', 'M', 'L']
        ]);

        \App\Models\Product::create([
            'name' => 'Floral Ruffle Top',
            'description' => 'Delicate white ruffle top with floral accents.',
            'price' => 899.00,
            'category_id' => $tops->id,
            'images' => ['/images/products/floral_ruffle_top.png'],
            'stock' => 20,
            'isNew' => true,
            'sizes' => ['XS', 'S', 'M', 'L']
        ]);

        \App\Models\Product::create([
            'name' => 'Casual Silk Blouse',
            'description' => 'Premium silk blouse for a soft and casual look.',
            'price' => 1599.00,
            'category_id' => $tops->id,
            'images' => ['/images/products/casual_silk_blouse.png'],
            'stock' => 12,
            'sizes' => ['S', 'M', 'L']
        ]);

        \App\Models\Product::create([
            'name' => 'High-Waist Denim Jeans',
            'description' => 'Classic blue denim jeans with a flattering high-waist fit.',
            'price' => 1899.00,
            'originalPrice' => 2299.00,
            'category_id' => $bottoms->id,
            'images' => ['/images/products/denim_jeans.png'],
            'stock' => 25,
            'isOnSale' => true,
            'sizes' => ['24', '26', '28', '30', '32']
        ]);

        \App\Models\Product::create([
            'name' => 'Tailored Trousers',
            'description' => 'Sleek beige trousers for a sharp, tailored appearance.',
            'price' => 1499.00,
            'category_id' => $bottoms->id,
            'images' => ['/images/products/tailored_trousers.png'],
            'stock' => 18,
            'isFeatured' => true,
            'sizes' => ['XS', 'S', 'M', 'L']
        ]);
    }
}
