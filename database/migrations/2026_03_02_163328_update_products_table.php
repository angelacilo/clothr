<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
            if (!Schema::hasColumn('products', 'sale_price')) {
                $table->decimal('sale_price', 10, 2)->nullable()->after('price');
            }
            if (!Schema::hasColumn('products', 'status')) {
                $table->string('status')->default('active')->after('category_id');
            }
            if (!Schema::hasColumn('products', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
        });

        // Fix any empty/duplicate slugs before adding unique index
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $slug = Str::slug($product->name);
            $original = $slug;
            $i = 1;
            while (DB::table('products')->where('slug', $slug)->where('product_id', '!=', $product->product_id)->exists()) {
                $slug = $original . '-' . ($i++);
            }
            DB::table('products')->where('product_id', $product->product_id)->update(['slug' => $slug]);
        }

        // Now safe to add unique index
        if (!collect(DB::select("SHOW INDEX FROM products WHERE Key_name = 'products_slug_unique'"))->count()) {
            Schema::table('products', function (Blueprint $table) {
                $table->unique('slug');
            });
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique('products_slug_unique');
            $table->dropColumn(['slug', 'sale_price', 'status', 'is_featured']);
        });
    }
}