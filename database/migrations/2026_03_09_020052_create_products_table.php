<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('originalPrice', 10, 2)->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->text('images')->nullable(); // JSON array of URLs
            $table->integer('stock')->default(0);
            $table->boolean('isNew')->default(false);
            $table->boolean('isOnSale')->default(false);
            $table->boolean('isFeatured')->default(false);
            $table->boolean('isArchived')->default(false);
            $table->text('sizes')->nullable(); // JSON array of sizes
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
