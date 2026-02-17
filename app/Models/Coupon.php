<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    protected $primaryKey = 'coupon_id';
    protected $fillable = ['code', 'discount_type', 'discount_value', 'expires_at', 'is_active'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'coupon_id', 'coupon_id');
    }
}
