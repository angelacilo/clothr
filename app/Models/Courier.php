<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $fillable = ['name', 'code', 'user_id'];

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function riders() { 
        return $this->hasMany(Rider::class); 
    }

    public function orders() { 
        // Assumes Order has 'courier_service' column
        return $this->hasMany(Order::class, 'courier_service', 'code'); 
    }
}
