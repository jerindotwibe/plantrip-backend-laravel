<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = [
        'country_id', 'name', 'slug', 'image', 'badge',
        'rating', 'locations_count', 'description', 'highlights', 'is_active',
    ];

    protected $casts = [
        'highlights' => 'array',
        'is_active'  => 'boolean',
        'rating'     => 'float',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function dayTours()
    {
        return $this->hasMany(DayTour::class);
    }
}
