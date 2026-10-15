<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_value',
        'min_amount', 'max_uses', 'uses_count', 'expires_at', 'is_active',
    ];

    protected $casts = [
        'discount_value' => 'float',
        'min_amount'     => 'float',
        'is_active'      => 'boolean',
        'expires_at'     => 'datetime',
    ];

    /**
     * Check if this promo code is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            return false;
        }
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount(float $amount): float
    {
        if ($amount < $this->min_amount) {
            return 0;
        }
        if ($this->discount_type === 'percentage') {
            return round($amount * ($this->discount_value / 100), 2);
        }
        return min($this->discount_value, $amount);
    }
}
