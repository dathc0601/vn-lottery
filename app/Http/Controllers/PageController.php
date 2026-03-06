<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Services\PageService;

class PageController extends Controller
{
    public function __construct(
        protected PageService $pageService
    ) {}

    public function show(string $slug)
    {
        $page = $this->pageService->getPageBySlug($slug);

        if (!$page) {
            abort(404);
        }

        $this->pageService->incrementViewCount($page);

        $relatedPages = $this->pageService->getRelatedPages($page, 4);

        $northProvinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $centralProvinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $southProvinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('pages.show', compact(
            'page',
            'relatedPages',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }
}
