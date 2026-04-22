<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRiderIdToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'rider_id')) {
                $table->foreignId('rider_id')->nullable()->constrained('riders')->nullOnDelete();
            } else {
                // If it exists, ensure it's constrained (optional, but requested by user flow)
                // Note: Modifying existing column types safely without doctrine/dbal is hard,
                // so we just ensure it's present for now.
            }
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'rider_id')) {
                $table->dropColumn('rider_id');
            }
        });
    }
}
