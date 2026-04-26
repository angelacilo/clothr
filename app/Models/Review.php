<?php

/**
 * FILE: Review.php
 * 
 * What this file is:
 * This model represents a customer's feedback on a product.
 * It includes a star rating (1 to 5) and an optional text comment.
 * 
 * How it connects to the project:
 * - Customers write reviews for products they have received.
 * - The system uses these reviews to calculate the average star rating of a product.
 * - Admins can hide reviews if they are inappropriate.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Columns for user feedback.
     * "is_visible" allows the admin to hide a review without deleting it.
     */
    protected $fillable = ['user_id', 'product_id', 'rating', 'comment', 'is_visible'];

    /**
     * CASTS: Ensures the database data is used as the correct type in PHP.
     */
    protected $casts = [
        'is_visible' => 'boolean',
        'rating'     => 'integer',
    ];

    /**
     * RELATIONSHIP: A review was written by one User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * RELATIONSHIP: A review is for one specific Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
