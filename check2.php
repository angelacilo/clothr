<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();
$log = file_get_contents(storage_path("logs/laravel.log"));
$lines = explode("\n", $log);
$last = array_slice($lines, -30);
echo implode("\n", $last);

