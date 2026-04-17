<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "--- Existing Portal Accounts ---\n";
echo "Couriers:\n";
foreach (User::where('role', 'courier')->get() as $u) {
    echo " - " . $u->email . " (Name: " . $u->name . ")\n";
}

echo "\nRiders:\n";
foreach (User::where('role', 'rider')->get() as $u) {
    echo " - " . $u->email . " (Name: " . $u->name . ")\n";
}
