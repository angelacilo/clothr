<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== PRODUCTS ===\n";
foreach (\App\Models\Product::all(["name","category_id"]) as $p) {
    echo $p->name . " -> category_id: " . $p->category_id . "\n";
}

echo "\n=== CATEGORIES WITH COUNT ===\n";
foreach (\App\Models\Category::withCount("products")->get() as $c) {
    echo $c->category_name . " -> products_count: " . $c->products_count . "\n";
}

