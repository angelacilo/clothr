<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixOrdersTableColumnsForDeliverySystem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Check if courier_name exists and rename it to courier_service if courier_service doesn't exist
            if (Schema::hasColumn('orders', 'courier_name') && !Schema::hasColumn('orders', 'courier_service')) {
                // We use raw SQL for renaming to avoid doctrine/dbal dependency issues common in older Laravel environments
                \DB::statement('ALTER TABLE orders CHANGE courier_name courier_service VARCHAR(255) NULL');
            } elseif (!Schema::hasColumn('orders', 'courier_service')) {
                $table->string('courier_service')->nullable()->after('status');
            }

            // Ensure rider_id exists
            if (!Schema::hasColumn('orders', 'rider_id')) {
                $table->unsignedBigInteger('rider_id')->nullable()->after('courier_service');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'courier_service')) {
                \DB::statement('ALTER TABLE orders CHANGE courier_service courier_name VARCHAR(255) NULL');
            }
        });
    }
}
