<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get all settings from cache or database.
     */
    public static function getAllSettings(): array
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('system_settings_all', function () {
            if (!\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
                return [];
            }
            return self::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get a setting value.
     */
    public static function getVal(string $key, $default = null)
    {
        $settings = self::getAllSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    /**
     * Set a setting value.
     */
    public static function setVal(string $key, $value): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        \Illuminate\Support\Facades\Cache::forget('system_settings_all');
        return $setting;
    }
}
