<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DayTour extends Model
{
    protected $fillable = [
        'country_id', 'destination_id', 'slug', 'title', 'location',
        'image', 'badge', 'duration', 'description', 'features',
        'inclusions', 'exclusions', 'itinerary',
        'rating', 'reviews_count', 'price', 'currency', 'is_active',
    ];

    protected $casts = [
        'features'      => 'array',
        'inclusions'    => 'array',
        'exclusions'    => 'array',
        'itinerary'     => 'array',
        'is_active'     => 'boolean',
        'rating'        => 'float',
        'price'         => 'float',
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
        return $this->hasMany(Booking::class, 'tour_id');
    }
}
