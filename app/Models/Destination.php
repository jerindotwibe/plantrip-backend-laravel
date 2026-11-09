<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    protected $fillable = [
        'country_id', 'name', 'slug', 'image', 'badge',
        'rating', 'locations_count', 'description', 'highlights', 'is_active', 'is_featured',
    ];

    protected $casts = [
        'highlights'  => 'array',
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'rating'      => 'float',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function dayTours()
    {
        return $this->hasMany(DayTour::class);
    }

    public function tourPackages()
    {
        return $this->hasMany(TourPackage::class);
    }

    public function hotels()
    {
        return $this->hasMany(Hotel::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }
}
