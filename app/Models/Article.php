<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'excerpt',
        'content',
        'featured_image',
        'author_id',
        'category_id',
        'status',
        'is_featured',
        'published_at',
        'view_count',
        'sort_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public const CACHE_KEY_PREFIX = 'article_';
    public const CACHE_TTL = 3600; // 1 hour

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function ($article) {
            static::clearCache($article->slug);
        });

        static::deleted(function ($article) {
            static::clearCache($article->slug);
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    /**
     * Scope for published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope for featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for ordering by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderByDesc('published_at');
    }

    /**
     * Scope for ordering by popularity (view count).
     */
    public function scopePopular($query)
    {
        return $query->orderByDesc('view_count');
    }

    /**
     * Get article by slug with caching.
     */
    public static function getBySlug(string $slug): ?self
    {
        return Cache::remember(
            self::CACHE_KEY_PREFIX . $slug,
            self::CACHE_TTL,
            function () use ($slug) {
                return static::with(['author', 'category'])
                    ->where('slug', $slug)
                    ->published()
                    ->first();
            }
        );
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
        static::clearCache($this->slug);
    }

    /**
     * Calculate estimated reading time in minutes.
     */
    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // 200 words per minute

        return max(1, $readingTime);
    }

    /**
     * Get related articles (same category, excluding current).
     */
    public function getRelatedArticles(int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        if (!$this->category_id) {
            return static::published()
                ->where('id', '!=', $this->id)
                ->latest()
                ->limit($limit)
                ->get();
        }

        return static::published()
            ->where('category_id', $this->category_id)
            ->where('id', '!=', $this->id)
            ->latest()
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
