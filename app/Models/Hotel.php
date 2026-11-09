<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    protected $fillable = [
        'country_id', 'destination_id', 'slug', 'title', 'location', 'image',
        'category', 'stars', 'destination', 'badge',
        'rating', 'reviews_count', 'price_per_night', 'currency',
        'amenities', 'description', 'policies', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'amenities'       => 'array',
        'is_active'       => 'boolean',
        'is_featured'     => 'boolean',
        'rating'          => 'float',
        'price_per_night' => 'float',
    ];

    protected $appends = ['price'];

    public function getPriceAttribute()
    {
        return $this->price_per_night;
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }
}
