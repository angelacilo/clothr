<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddOutForDeliveryToOrdersStatusEnum extends Migration
{
    public function up()
    {
        // Data cleanup: ensure all statuses are lowercase and match enum values
        DB::statement("UPDATE orders SET status = LOWER(status)");
        
        // Map any legacy or missing statuses if necessary
        // (Assuming current ones map directly to lowercase versions)

        // Using raw SQL for enum modification in Laravel 8 without doctrine/dbal
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status VARCHAR(255) DEFAULT 'pending'");
    }
}
