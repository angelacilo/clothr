<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

echo "=== ORDERS TABLE ===\n";
$cols = DB::select("SHOW COLUMNS FROM orders");
foreach($cols as $c) echo $c->Field . " (" . $c->Type . ")\n";

echo "\n=== ORDER ITEMS TABLE ===\n";
$cols = DB::select("SHOW COLUMNS FROM order_items");
foreach($cols as $c) echo $c->Field . " (" . $c->Type . ")\n";

echo "\n=== SAMPLE ORDERS ===\n";
$rows = DB::select("SELECT * FROM orders LIMIT 3");
foreach($rows as $r) print_r($r);

