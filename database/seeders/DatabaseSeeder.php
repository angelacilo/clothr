<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::create([
            'name' => 'Admin User',
            'email' => 'admin@clothr.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            'is_admin' => true,
        ]);

        \App\Models\User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'is_admin' => false,
        ]);

        $this->call(\Database\Seeders\ProductSeeder::class);

        // Add some sample orders for the dashboard
        \App\Models\Order::create([
            'customer_info' => ['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'phone' => '09123456789', 'address' => '123 Makati Ave', 'city' => 'Manila', 'zip' => '1200'],
            'items' => [
                ['name' => 'Floral Summer Dress', 'price' => 1299.00, 'quantity' => 1, 'size' => 'M'],
                ['name' => 'Chic Midi Dress', 'price' => 2499.00, 'quantity' => 1, 'size' => 'S']
            ],
            'total' => 3798.00, 
            'status' => 'Delivered',
            'created_at' => now()->subDays(2),
        ]);

        \App\Models\Order::create([
            'customer_info' => ['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane@example.com', 'phone' => '09876543210', 'address' => '456 Taft Ave', 'city' => 'Manila', 'zip' => '1000'],
            'items' => [
                ['name' => 'High-Waist Denim Jeans', 'price' => 1899.00, 'quantity' => 1, 'size' => '28']
            ],
            'total' => 2149.00, // Includes 250 shipping
            'status' => 'Pending',
            'created_at' => now()->subDay(),
        ]);
    }
}
