<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class NewsService
{
    /**
     * Get published articles with pagination.
     */
    public function getPublishedArticles(int $perPage = 12, ?int $categoryId = null): LengthAwarePaginator
    {
        $query = Article::published()
            ->with(['author', 'category'])
            ->latest();

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a single article by slug.
     */
    public function getArticleBySlug(string $slug): ?Article
    {
        return Article::getBySlug($slug);
    }

    /**
     * Get recent articles.
     */
    public function getRecentArticles(int $limit = 5, ?int $excludeId = null): Collection
    {
        $query = Article::published()
            ->with(['category'])
            ->latest();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get featured articles.
     */
    public function getFeaturedArticles(int $limit = 4): Collection
    {
        return Article::published()
            ->featured()
            ->with(['author', 'category'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get popular articles (by view count).
     */
    public function getPopularArticles(int $limit = 5, ?int $excludeId = null): Collection
    {
        $query = Article::published()
            ->with(['category'])
            ->popular();

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get related articles for a given article.
     */
    public function getRelatedArticles(Article $article, int $limit = 4): Collection
    {
        return $article->getRelatedArticles($limit);
    }

    /**
     * Get all active categories.
     */
    public function getActiveCategories(): Collection
    {
        return ArticleCategory::getAllCached();
    }

    /**
     * Get categories with article counts.
     */
    public function getCategoriesWithCounts(): Collection
    {
        return ArticleCategory::getWithArticleCounts();
    }

    /**
     * Increment article view count.
     */
    public function incrementViewCount(Article $article): void
    {
        $article->incrementViewCount();
    }

    /**
     * Get articles by category slug.
     */
    public function getArticlesByCategorySlug(string $categorySlug, int $perPage = 12): ?LengthAwarePaginator
    {
        $category = ArticleCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            return null;
        }

        return Article::published()
            ->where('category_id', $category->id)
            ->with(['author', 'category'])
            ->latest()
            ->paginate($perPage);
    }
}
