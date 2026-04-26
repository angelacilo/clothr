<?php

/**
 * FILE: Category.php
 * 
 * What this file is:
 * This is the blueprint for a Product Category (like "Dresses", "Shirts", "Shoes").
 * 
 * How it connects to the project:
 * - Products belong to these categories.
 * - Categories are shown in the navigation menu on the website.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Columns that we can save into from the Admin panel.
     */
    protected $fillable = ['name', 'slug', 'isVisible'];

    /**
     * CASTS: Converts the database value (0 or 1) into a real true/false in PHP.
     */
    protected $casts = [
        'isVisible' => 'boolean',
    ];

    /**
     * RELATIONSHIP: One category can have MANY products.
     * Example: The "Men" category has many different shirts and pants.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
