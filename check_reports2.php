<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
echo "=== SUMMARY ===\n";
$orders = App\Models\Order::whereNotIn("order_status", ["cancelled"])->get();
echo "Orders: " . $orders->count() . "\n";
echo "Revenue: " . $orders->sum("total_amount") . "\n";
echo "\n=== LOW STOCK ===\n";
$low = App\Models\Inventory::with("product.category")->where("available_qty", "<=", 5)->get();
foreach($low as $i) echo $i->product->name . " - " . $i->available_qty . "\n";
echo "\n=== ORDER STATUS ===\n";
$status = DB::table("orders")->select("order_status", DB::raw("COUNT(*) as count"))->groupBy("order_status")->get();
foreach($status as $s) echo $s->order_status . ": " . $s->count . "\n";

