<?php

namespace App\Services;

use App\Models\Page;
use Illuminate\Database\Eloquent\Collection;

class PageService
{
    public function getPageBySlug(string $slug): ?Page
    {
        return Page::getBySlug($slug);
    }

    public function incrementViewCount(Page $page): void
    {
        $page->incrementViewCount();
    }

    public function getRelatedPages(Page $page, int $limit = 4): Collection
    {
        return $page->getRelatedPages($limit);
    }

    public function getAllPublishedPages(): Collection
    {
        return Page::published()
            ->ordered()
            ->get();
    }
}
