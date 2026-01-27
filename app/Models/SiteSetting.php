<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    public const CACHE_KEY = 'site_settings_all';
    public const CACHE_TTL = 3600; // 1 hour

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Get all settings as a cached nested array: [group => [key => value]]
     */
    public static function getAllCached(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $settings = [];

            foreach (static::all() as $setting) {
                $settings[$setting->group][$setting->key] = $setting->value;
            }

            return $settings;
        });
    }

    /**
     * Get a single setting value by group and key
     */
    public static function getValue(string $group, string $key, ?string $default = null): ?string
    {
        $all = static::getAllCached();

        return $all[$group][$key] ?? $default;
    }

    /**
     * Get all settings for a group
     */
    public static function getGroup(string $group): array
    {
        $all = static::getAllCached();

        return $all[$group] ?? [];
    }

    /**
     * Set a setting value (creates or updates)
     */
    public static function setValue(string $group, string $key, ?string $value, string $type = 'text'): void
    {
        static::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    /**
     * Clear the settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
