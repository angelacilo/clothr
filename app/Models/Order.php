<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['customer_info', 'items', 'total', 'status'];

    protected $casts = [
        'customer_info' => 'array',
        'items' => 'array',
        'total' => 'float',
    ];
}
