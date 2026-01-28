<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class ArticleCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const CACHE_KEY = 'article_categories_all';
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

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id');
    }

    public function publishedArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'category_id')
            ->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Get all active categories, cached.
     */
    public static function getAllCached(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all active categories with article counts.
     */
    public static function getWithArticleCounts(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->withCount(['articles' => function ($query) {
                $query->where('status', 'published')
                    ->where('published_at', '<=', now());
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
