<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

// Show reviews table columns
$cols = DB::select("SHOW COLUMNS FROM reviews");
echo "=== REVIEWS COLUMNS ===\n";
foreach($cols as $c) echo $c->Field . " (" . $c->Type . ")\n";

// Show sample data
echo "\n=== SAMPLE REVIEWS ===\n";
$rows = DB::select("SELECT * FROM reviews LIMIT 3");
foreach($rows as $r) print_r($r);

