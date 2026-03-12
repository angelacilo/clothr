<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'price', 'originalPrice', 'category_id',
        'images', 'stock', 'isNew', 'isOnSale', 'isFeatured', 'isArchived', 'sizes', 'colors',
        'sales_count', 'isTrending'
    ];

    protected $casts = [
        'images' => 'array',
        'sizes' => 'array',
        'colors' => 'array',
        'isNew' => 'boolean',
        'isOnSale' => 'boolean',
        'isFeatured' => 'boolean',
        'isArchived' => 'boolean',
        'isTrending' => 'boolean',
        'price' => 'float',
        'originalPrice' => 'float',
        'sales_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?: 0;
    }
}
