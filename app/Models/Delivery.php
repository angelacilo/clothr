<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id', 'rider_id', 'status',
        'assigned_at', 'released_at', 'picked_up_at', 'delivered_at', 'notes', 'proof_of_delivery'
    ];

    protected $casts = [
        'assigned_at'  => 'datetime',
        'released_at'  => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order() { 
        return $this->belongsTo(Order::class); 
    }

    public function rider() { 
        return $this->belongsTo(Rider::class); 
    }
}
