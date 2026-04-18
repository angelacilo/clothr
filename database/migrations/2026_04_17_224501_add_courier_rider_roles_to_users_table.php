<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddCourierRiderRolesToUsersTable extends Migration
{
    public function up()
    {
        // 1. Add the 'role' column if it doesn't exist
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                // Initialize with 'customer' or 'admin' based on 'is_admin' if possible
                $table->string('role')->default('customer')->after('is_admin');
            });
        }

        // 2. Data cleanup: convert 'user' to 'customer', sync with is_admin
        // We do this while it's still a VARCHAR to avoid ENUM constraint issues during transition
        DB::table('users')->where('role', 'user')->update(['role' => 'customer']);
        DB::table('users')->whereNull('role')->update(['role' => 'customer']);
        DB::table('users')->where('role', '')->update(['role' => 'customer']);
        
        if (Schema::hasColumn('users', 'is_admin')) {
            DB::table('users')->where('is_admin', true)->update(['role' => 'admin']);
        }

        // 3. Finally, modify it to be an ENUM for strictness
        // Laravel 8 change() on enums requires doctrine/dbal. 
        // Using DB::statement for compatibility as doctrine/dbal installation was blocked.
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'courier', 'rider') DEFAULT 'customer'");
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
}
