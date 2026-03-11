<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$cols = DB::select("SHOW COLUMNS FROM users");
echo "=== USERS COLUMNS ===\n";
foreach($cols as $c) echo $c->Field . " (" . $c->Type . ")\n";
$rows = DB::select("SELECT * FROM users LIMIT 2");
echo "\n=== SAMPLE ===\n";
foreach($rows as $r) print_r($r);

