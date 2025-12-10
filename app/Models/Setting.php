<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description'
    ];

    /**
     * Get setting value by key
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Cast value based on type
        return match($setting->type) {
            'number' => (float) $setting->value,
            'boolean' => (bool) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value
        };
    }
    
    /**
     * Set setting value by key
     */
    public static function set($key, $value, $type = 'string', $description = null)
    {
        // Convert value to string for storage
        $stringValue = match($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value
        };
        
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $stringValue,
                'type' => $type,
                'description' => $description
            ]
        );
    }
    
    /**
     * Get service charge percentage
     */
    public static function getServiceChargePercentage()
    {
        return self::get('service_charge_percentage', 3);
    }
    
    /**
     * Set service charge percentage
     */
    public static function setServiceChargePercentage($percentage)
    {
        return self::set(
            'service_charge_percentage', 
            $percentage, 
            'number', 
            'Service charge percentage (0-100)'
        );
    }
}