<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rider extends Model
{
    protected $fillable = ['user_id', 'courier_id', 'phone', 'is_available'];

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function courier() { 
        return $this->belongsTo(Courier::class); 
    }

    public function deliveries() { 
        return $this->hasMany(Delivery::class); 
    }

    public function activeDeliveries() {
        return $this->hasMany(Delivery::class)->whereNotIn('status', ['delivered', 'failed']);
    }
}
