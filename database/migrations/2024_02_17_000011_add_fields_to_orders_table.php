<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email');
            $table->unsignedBigInteger('address_id')->nullable();
            $table->foreign('address_id')->references('address_id')->on('addresses');
            $table->text('order_description')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('tracking_num')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->foreign('coupon_id')->references('coupon_id')->on('coupons');
            $table->decimal('discount_amount', 10, 2)->default(0);
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
            $table->dropForeign(['address_id', 'coupon_id']);
            $table->dropColumn(['first_name', 'last_name', 'phone_number', 'email', 'address_id', 'order_description', 'shipping_address', 'tracking_num', 'coupon_id', 'discount_amount']);
        });
    }
}
