<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $fillable = [
        'country_id', 'slug', 'title', 'subtitle', 'image', 'badge',
        'duration', 'duration_days', 'duration_nights',
        'price', 'currency', 'includes', 'itinerary', 'highlights', 'terms', 'is_active',
    ];

    protected $casts = [
        'includes'   => 'array',
        'itinerary'  => 'array',
        'highlights' => 'array',
        'is_active'  => 'boolean',
        'price'      => 'float',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_id');
    }
}
