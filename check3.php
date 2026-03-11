<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$log = file_get_contents(storage_path("logs/laravel.log"));
preg_match_all("/local\.ERROR: ([^\{]+)/", $log, $matches);
$last = array_slice($matches[1], -5);
foreach($last as $e) echo $e . "\n---\n";

