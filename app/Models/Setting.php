<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /**
     * Helper to get a setting by key with optional default
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper to set a setting value
     */
    public static function set(string $key, $value, string $group = 'general'): self
    {
        return static::updateOrCreate(['key' => $key], [
            'value' => is_array($value) ? json_encode($value) : $value,
            'group' => $group,
        ]);
    }
}

