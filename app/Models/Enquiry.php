<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'full_name', 'email', 'phone', 'country', 'destination',
        'travel_type', 'start_date', 'duration', 'message', 'status', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];
}
