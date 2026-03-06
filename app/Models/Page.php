<?php

namespace App\Models;

use App\Traits\ReplacesContentPlaceholders;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
    use ReplacesContentPlaceholders;
    protected $fillable = [
        'slug',
        'title',
        'content',
        'featured_image',
        'status',
        'published_at',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public const CACHE_KEY_PREFIX = 'page_';
    public const CACHE_TTL = 3600;

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($page) {
            static::clearCache($page->slug);
        });

        static::deleted(function ($page) {
            static::clearCache($page->slug);
        });
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public static function getBySlug(string $slug): ?self
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX . $slug,
            self::CACHE_TTL,
            function () use ($slug) {
                return static::where('slug', $slug)
                    ->published()
                    ->first();
            }
        );
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        static::clearCache($this->slug);
    }

    public function getRelatedPages(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public static function clearCache(?string $slug = null): void
    {
        if ($slug) {
            Cache::forget(self::CACHE_KEY_PREFIX . $slug);
        }
    }
}
