<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Courier;
use App\Models\Rider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

try {
    DB::transaction(function() {
        // 1. Create Courier User
        $user = User::updateOrCreate(
            ['email' => 'jt@clothr.com'],
            [
                'name' => 'J&T Express Manager',
                'password' => Hash::make('password123'),
                'role' => 'courier'
            ]
        );

        // 2. Create Courier Company Profile
        $courier = Courier::updateOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'J&T Express',
                'code' => 'JT'
            ]
        );

        // 3. Link existing riders to this courier for testing
        Rider::whereIn('id', [1, 2, 3])->update(['courier_id' => $courier->id]);

        echo "Test Courier Account Created Successfully!\n";
        echo "Email: jt@clothr.com\n";
        echo "Password: password123\n";
        echo "Please use these credentials to log in at /courier/login\n";
    });
} catch (\Exception $e) {
    echo "Error seeding test courier: " . $e->getMessage() . "\n";
}
