<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Services\NewsService;

class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService
    ) {}

    public function index()
    {
        $featuredArticles = $this->newsService->getFeaturedArticles(3);
        $articles = $this->newsService->getPublishedArticles(12);
        $categories = $this->newsService->getCategoriesWithCounts();
        $popularArticles = $this->newsService->getPopularArticles(5);

        return view('news.index', compact(
            'featuredArticles',
            'articles',
            'categories',
            'popularArticles'
        ));
    }

    public function category(string $categorySlug)
    {
        $category = ArticleCategory::where('slug', $categorySlug)
            ->where('is_active', true)
            ->first();

        if (!$category) {
            abort(404);
        }

        $articles = $this->newsService->getPublishedArticles(12, $category->id);
        $categories = $this->newsService->getCategoriesWithCounts();
        $popularArticles = $this->newsService->getPopularArticles(5);

        return view('news.category', compact(
            'category',
            'articles',
            'categories',
            'popularArticles'
        ));
    }

    public function show(string $slug)
    {
        $article = $this->newsService->getArticleBySlug($slug);

        if (!$article) {
            abort(404);
        }

        // Increment view count
        $this->newsService->incrementViewCount($article);

        $relatedArticles = $this->newsService->getRelatedArticles($article, 4);
        $categories = $this->newsService->getCategoriesWithCounts();
        $popularArticles = $this->newsService->getPopularArticles(5, $article->id);

        return view('news.show', compact(
            'article',
            'relatedArticles',
            'categories',
            'popularArticles'
        ));
    }
}
