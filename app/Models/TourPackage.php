<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    protected $fillable = [
        'country_id', 'destination_id', 'slug', 'title', 'subtitle', 'image', 'badge',
        'duration', 'duration_days', 'duration_nights', 'description',
        'price', 'currency', 'includes', 'itinerary', 'highlights', 'terms', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'includes'    => 'array',
        'itinerary'   => 'array',
        'highlights'  => 'array',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'price'       => 'float',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'package_id');
    }
}
