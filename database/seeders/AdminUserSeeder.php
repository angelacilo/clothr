<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an admin user for testing
        User::firstOrCreate(
            ['email' => 'admin@clothr.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone_num' => '123456789',
            ]
        );

        // Create a regular customer user for testing
        User::firstOrCreate(
            ['email' => 'customer@clothr.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'role' => 'customer',
                'phone_num' => '987654321',
            ]
        );
    }
}
