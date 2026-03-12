<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocationsTables extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('regions');
        Schema::dropIfExists('countries');
    }
}
