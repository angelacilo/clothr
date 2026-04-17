<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "--- TABLES ---\n";
$tables = DB::select('show tables');
foreach($tables as $table) {
    echo array_values((array)$table)[0] . "\n";
}

echo "\n--- SYSTEM ACCOUNTS ---\n";
$users = User::whereIn('role', ['admin', 'courier', 'rider'])->get(['id', 'name', 'email', 'role']);
echo json_encode($users, JSON_PRETTY_PRINT) . "\n";

echo "\n--- COURIER COMPANIES ---\n";
try {
    $couriers = DB::table('couriers')->get();
    echo json_encode($couriers, JSON_PRETTY_PRINT) . "\n";
} catch (\Exception $e) {
    echo "Couriers table check failed: " . $e->getMessage() . "\n";
}

echo "\n--- ORDERS TABLE COLUMNS ---\n";
try {
    $columns = DB::select('show columns from orders');
    foreach($columns as $col) {
        echo $col->Field . " (" . $col->Type . ")\n";
    }
} catch (\Exception $e) {
    echo "Orders column check failed: " . $e->getMessage() . "\n";
}

echo "\n--- DELIVERIES TABLE COLUMNS ---\n";
try {
    $columns = DB::select('show columns from deliveries');
    foreach($columns as $col) {
        echo $col->Field . " (" . $col->Type . ")\n";
    }
} catch (\Exception $e) {
    echo "Deliveries column check failed: " . $e->getMessage() . "\n";
}
