<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'name', 'email', 'trip', 'destination', 'rating', 'comment', 'date', 'avatar',
        'status', 'admin_note', 'is_featured', 'is_active',
    ];

    protected $casts = [
        'rating'      => 'integer',
        'is_featured' => 'boolean',
        'is_active'   => 'boolean',
    ];
}

