<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_info', 'items', 'total', 'status',
        'courier_name', 'tracking_number',
        'processing_at', 'shipped_at', 'delivered_at', 'cancelled_at',
    ];

    protected $casts = [
        'customer_info' => 'array',
        'items' => 'array',
        'total' => 'float',
        'processing_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tracking URL for the assigned courier.
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
}
