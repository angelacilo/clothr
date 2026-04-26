<?php

/**
 * FILE: Order.php
 * WHAT IT DOES: This is the "Blueprint" for a Customer Order.
 * WHY: To save details about what people bought, how much they paid, and where they live.
 * HOW IT WORKS: It stores the order items, customer info, and status (Pending, Shipped, etc.) in the database.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * FILLABLE: Columns that we are allowed to save data into.
     */
    protected $fillable = [
        'user_id', 'customer_info', 'items', 'total', 'status',
        'courier_name', 'courier_service', 'tracking_number', 'rider_id',
        'processing_at', 'shipped_at', 'picked_up_at', 'out_for_delivery_at', 'delivered_at', 'cancelled_at',
    ];

    /**
     * CASTS: Tells Laravel to treat "items" as an array (list) and "total" as a decimal number.
     * It also converts date strings into actual "Carbon" date objects.
     */
    protected $casts = [
        'customer_info' => 'array',
        'items' => 'array',
        'total' => 'float',
        'processing_at' => 'datetime',
        'shipped_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * RELATIONSHIP: An order belongs to a specific User (Customer).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * WHAT IT DOES: Automatically creates a link to the courier's website (like J&T or LBC).
     * WHY: So the customer can click it to see exactly where their package is.
     */
    public function getTrackingUrlAttribute()
    {
        if (!$this->tracking_number || !$this->courier_name) {
            return null;
        }

        $urls = [
            'J&T Express'   => 'https://www.jtexpress.ph/trajectoryQuery?billcode=' . $this->tracking_number,
            'LBC Express'    => 'https://www.lbcexpress.com/track/?tracking_no=' . $this->tracking_number,
            'Ninja Van'      => 'https://www.ninjavan.co/en-ph/tracking?id=' . $this->tracking_number,
            'Flash Express'  => 'https://www.flashexpress.ph/fle/tracking?se=' . $this->tracking_number,
            'Grab Express'   => null,
            'Lalamove'       => null,
            'GoGo Xpress'    => 'https://gogoxpress.com/track?tracking_number=' . $this->tracking_number,
            'Local Rider'    => null,
        ];

        return $urls[$this->courier_name] ?? null;
    }

    /**
     * RELATIONSHIP: If a local rider is assigned, this order belongs to that Rider.
     */
    public function rider() { 
        return $this->belongsTo(Rider::class); 
    }

    /**
     * RELATIONSHIP: Connects the order to the Delivery details.
     */
    public function delivery() { 
        return $this->hasOne(Delivery::class); 
    }
}
