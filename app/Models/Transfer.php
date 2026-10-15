<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'country_id', 'slug', 'title', 'from_location', 'to_location',
        'duration', 'price', 'currency', 'image', 'vehicles', 'description', 'is_active',
    ];

    protected $casts = [
        'vehicles'  => 'array',
        'is_active' => 'boolean',
        'price'     => 'float',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
