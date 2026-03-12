<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('label')->default('Home'); // e.g. Home, Work
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('country');
            $table->string('region')->nullable();
            $table->string('city');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('zip_code');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
