<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'country_id', 'slug', 'title', 'location', 'image',
        'category', 'stars', 'destination', 'badge',
        'rating', 'reviews_count', 'price_per_night', 'currency',
        'amenities', 'description', 'policies', 'is_active',
    ];

    protected $casts = [
        'amenities'  => 'array',
        'is_active'  => 'boolean',
        'rating'     => 'float',
        'price_per_night' => 'float',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
