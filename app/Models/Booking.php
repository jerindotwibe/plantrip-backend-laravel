<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'booking_reference', 'user_id', 'tour_id', 'package_id',
        'first_name', 'last_name', 'email', 'phone', 'country_code',
        'whatsapp', 'nationality',
        'destination', 'country', 'travel_date',
        'adults', 'children', 'pick_up', 'drop_point', 'special_requests',
        'payment_method', 'promo_code',
        'subtotal', 'discount', 'taxes', 'total_amount', 'currency',
        'status', 'payment_status', 'admin_notes',
    ];

    protected $casts = [
        'travel_date'  => 'date',
        'subtotal'     => 'float',
        'discount'     => 'float',
        'taxes'        => 'float',
        'total_amount' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tour()
    {
        return $this->belongsTo(DayTour::class, 'tour_id');
    }

    public function package()
    {
        return $this->belongsTo(TourPackage::class, 'package_id');
    }

    /**
     * Generate a unique booking reference like BK-2026-0001
     */
    public static function generateReference(): string
    {
        $year = date('Y');
        $last = static::whereYear('created_at', $year)->count() + 1;
        return sprintf('BK-%s-%04d', $year, $last);
    }
}
