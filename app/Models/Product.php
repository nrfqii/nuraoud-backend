<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category',
        'brand',
        'scent',
        'description',
        'image',
        'rating',
        'reviews',
        'stock',
        'bestseller',
        'volume'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:1',
        'bestseller' => 'boolean',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }
}
