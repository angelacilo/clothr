<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCourierRiderRolesToUsersTable extends Migration
{
    public function up()
    {
        // Data cleanup: convert 'user' to 'customer', sync with is_admin
        DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
        DB::table('users')->whereNull('role')->update(['role' => 'customer']);
        DB::table('users')->where('role', '')->update(['role' => 'customer']);
        DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);

        // Laravel 8 change() on enums requires doctrine/dbal. 
        // Using DB::statement for compatibility as doctrine/dbal installation was blocked.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'courier', 'rider') DEFAULT 'customer'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(255) DEFAULT 'customer'");
    }
}
