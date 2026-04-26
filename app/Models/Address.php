<?php

/**
 * FILE: Address.php
 * 
 * What this file is:
 * This model represents a saved shipping address for a customer.
 * 
 * How it connects to the project:
 * - A User can have multiple addresses (Home, Office, etc.).
 * - One address can be marked as "Default" for faster checkout.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Columns that store the location details.
     * "label" is the name given by the user (like "Work").
     */
    protected $fillable = [
        'user_id', 'label', 'first_name', 'last_name', 'phone', 
        'country', 'region', 'city', 'address_line_1', 'address_line_2', 
        'zip_code', 'is_default'
    ];

    /**
     * CASTS: Converts the is_default number into a true/false value.
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * RELATIONSHIP: This address belongs to a specific User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
