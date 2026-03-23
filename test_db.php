<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Show all products with their id, name, sizes, colors
$products = App\Models\Product::whereNotNull('id')->orderBy('id')->get(['id','name','sizes','colors','isArchived']);
echo "=== ALL PRODUCTS ===\n";
foreach ($products as $p) {
    echo "ID: {$p->id} | {$p->name}\n";
    echo "  sizes:  " . json_encode($p->sizes) . "\n";
    echo "  colors: " . json_encode($p->colors) . "\n";
    echo "  archived: " . ($p->isArchived ? 'yes' : 'no') . "\n\n";
}
