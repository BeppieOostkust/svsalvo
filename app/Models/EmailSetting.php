<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailSetting extends Model
{
    protected $fillable = [
        'key',
        'name',
        'enabled',
        'description',
        'category',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, bool $default = true): bool
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->enabled : $default;
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, bool $enabled): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['enabled' => $enabled]
        );
    }

    /**
     * Check if email notification is enabled for a specific type
     */
    public static function isEnabled(string $key): bool
    {
        return self::get($key, true);
    }
}
