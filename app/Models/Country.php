<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name', 'slug', 'flag', 'currency', 'hero_image',
        'hero_title', 'tagline', 'description',
        'tour_count', 'package_count', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function destinations()
    {
        return $this->hasMany(Destination::class);
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
