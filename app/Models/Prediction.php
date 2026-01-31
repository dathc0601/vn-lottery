<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Prediction extends Model
{
    protected $fillable = [
        'region',
        'prediction_date',
        'reference_date',
        'article_id',
        'predictions_data',
        'analysis_data',
        'statistics_snapshot',
        'lottery_results_snapshot',
        'status',
        'generated_at',
        'published_at',
    ];

    protected $casts = [
        'prediction_date' => 'date',
        'reference_date' => 'date',
        'predictions_data' => 'array',
        'analysis_data' => 'array',
        'statistics_snapshot' => 'array',
        'lottery_results_snapshot' => 'array',
        'generated_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public const CACHE_KEY_PREFIX = 'prediction_';
    public const CACHE_TTL = 3600; // 1 hour

    public const REGION_NORTH = 'north';
    public const REGION_CENTRAL = 'central';
    public const REGION_SOUTH = 'south';

    public const REGIONS = [
        self::REGION_NORTH => 'Miền Bắc',
        self::REGION_CENTRAL => 'Miền Trung',
        self::REGION_SOUTH => 'Miền Nam',
    ];

    public const REGION_SLUGS = [
        self::REGION_NORTH => 'xsmb',
        self::REGION_CENTRAL => 'xsmt',
        self::REGION_SOUTH => 'xsmn',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($prediction) {
            static::clearCache($prediction->region, $prediction->prediction_date);
        });

        static::deleted(function ($prediction) {
            static::clearCache($prediction->region, $prediction->prediction_date);
        });
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * Scope for published predictions.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for a specific region.
     */
    public function scopeForRegion($query, string $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope for ordering by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('prediction_date');
    }

    /**
     * Get prediction by region and date with caching.
     */
    public static function getByRegionAndDate(string $region, string $date): ?self
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX . $region . '_' . $date,
            self::CACHE_TTL,
            function () use ($region, $date) {
                return static::with('article')
                    ->where('region', $region)
                    ->where('prediction_date', $date)
                    ->published()
                    ->first();
            }
        );
    }

    /**
     * Get region name in Vietnamese.
     */
    public function getRegionNameAttribute(): string
    {
        return self::REGIONS[$this->region] ?? $this->region;
    }

    /**
     * Get region slug (xsmb, xsmt, xsmn).
     */
    public function getRegionSlugAttribute(): string
    {
        return self::REGION_SLUGS[$this->region] ?? $this->region;
    }

    /**
     * Get formatted date for URL.
     */
    public function getDateSlugAttribute(): string
    {
        return $this->prediction_date->format('d-m-Y');
    }

    /**
     * Get formatted date for display.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->prediction_date->format('d/m/Y');
    }

    /**
     * Get the URL for this prediction.
     */
    public function getUrlAttribute(): string
    {
        return route("prediction.{$this->region_slug}.show", [
            'date' => $this->date_slug,
            'date2' => $this->date_slug,
        ]);
    }

    /**
     * Convert region slug to region key.
     */
    public static function regionFromSlug(string $slug): ?string
    {
        $map = array_flip(self::REGION_SLUGS);
        return $map[$slug] ?? null;
    }

    public static function clearCache(?string $region = null, $date = null): void
    {
        if ($region && $date) {
            $dateStr = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : $date;
            Cache::forget(self::CACHE_KEY_PREFIX . $region . '_' . $dateStr);
        }
    }
}
