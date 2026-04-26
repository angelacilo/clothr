<?php

/**
 * FILE: CartItem.php
 * 
 * What this file is:
 * This represents a single row in the shopping cart.
 * It stores which product the user picked, what size, what color, and how many.
 * 
 * How it connects to the project:
 * - It links a User to a Product.
 * - When a user logs in, their cart items are saved here.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Columns we allow to be saved.
     * "is_selected" is used for the checkboxes in the cart page.
     */
    protected $fillable = ['user_id', 'product_id', 'size', 'color', 'quantity', 'is_selected'];

    /**
     * RELATIONSHIP: This cart item belongs to a specific Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * RELATIONSHIP: This cart item belongs to a specific User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
