<?php

/**
 * FILE: Product.php
 * WHAT IT DOES: This is the "Blueprint" for a Product.
 * WHY: It tells Laravel which table in the database to talk to and which columns are available.
 * HOW IT WORKS: It maps the columns in the "products" table (like name, price, images) to variables we can use in PHP.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * FILLABLE: These are the columns we are allowed to save data into.
     * It prevents hackers from trying to save data into columns that should be secret.
     */
    protected $fillable = [
        'name', 'description', 'price', 'originalPrice', 'category_id',
        'images', 'stock', 'isNew', 'isOnSale', 'isFeatured', 'isArchived', 'sizes', 'colors',
        'variants', 'variant_stock', 'sales_count', 'isTrending'
    ];

    /**
     * CASTS: This is very important. 
     * It tells Laravel how to treat the data when it comes from the database.
     * For example, 'images' is stored as text in the DB, but we tell Laravel to treat it as an "array" (a list).
     */
    protected $casts = [
        'images'       => 'array',
        'sizes'        => 'array',
        'colors'       => 'array',
        'variants'     => 'array',
        'variant_stock'=> 'array',
        'isNew'        => 'boolean',
        'isOnSale'     => 'boolean',
        'isFeatured'   => 'boolean',
        'isArchived'   => 'boolean',
        'isTrending'   => 'boolean',
        'price'        => 'float',
        'originalPrice'=> 'float',
        'sales_count'  => 'integer',
    ];

    /**
     * RELATIONSHIP: A product belongs to a Category (e.g., "Men", "Women").
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * RELATIONSHIP: A product can have many Reviews from customers.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * WHAT IT DOES: Calculates the average star rating (1 to 5) based on customer reviews.
     */
    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }
}
