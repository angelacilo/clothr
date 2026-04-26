<?php

/**
 * FILE: Wishlist.php
 * 
 * What this file is:
 * This model represents a "Saved Item". It links a User to a Product 
 * that they are interested in but haven't bought yet.
 * 
 * How it connects to the project:
 * - It is used by the "Heart" button on the store.
 * - Admins can view wishlist statistics to see which products are popular.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory;

    /**
     * FILLABLE: We only need to store who (user_id) and what (product_id).
     */
    protected $fillable = ['user_id', 'product_id'];

    /**
     * RELATIONSHIP: This wishlist entry belongs to a User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATIONSHIP: This wishlist entry belongs to a Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
