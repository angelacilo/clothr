<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'originalPrice', 'category_id',
        'images', 'stock', 'isNew', 'isOnSale', 'isFeatured', 'isArchived', 'sizes'
    ];

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'isNew' => 'boolean',
        'isOnSale' => 'boolean',
        'isFeatured' => 'boolean',
        'isArchived' => 'boolean',
        'price' => 'float',
        'originalPrice' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
