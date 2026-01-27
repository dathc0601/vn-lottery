<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SeoOverride extends Model
{
    protected $fillable = [
        'path_pattern',
        'match_type',
        'label',
        'page_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'schema_jsonld',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'schema_jsonld' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    public const CACHE_KEY = 'seo_overrides_all';
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
     * Get all active overrides, cached.
     */
    public static function getAllCached(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::where('is_active', true)
                ->orderByDesc('priority')
                ->get();
        });
    }

    /**
     * Find the best matching override for a given path.
     * Exact matches are checked first, then wildcard matches.
     * Within each type, higher priority wins.
     */
    public static function findForPath(string $path): ?self
    {
        $path = self::normalizePath($path);
        $overrides = static::getAllCached();

        // 1. Exact match first (already sorted by priority desc)
        foreach ($overrides as $override) {
            if ($override->match_type === 'exact' && self::normalizePath($override->path_pattern) === $path) {
                return $override;
            }
        }

        // 2. Wildcard match second
        foreach ($overrides as $override) {
            if ($override->match_type === 'wildcard') {
                $normalized = self::normalizePath($override->path_pattern);
                $regex = str_replace('\*', '.*', preg_quote($normalized, '#'));
                if (preg_match('#^' . $regex . '$#', $path)) {
                    return $override;
                }
            }
        }

        return null;
    }

    /**
     * Normalize a path to always start with / and have no trailing slash (except root).
     */
    protected static function normalizePath(string $path): string
    {
        $path = '/' . ltrim(trim($path), '/');

        // Remove trailing slash unless it's the root "/"
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
