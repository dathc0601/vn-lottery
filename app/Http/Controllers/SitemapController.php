<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(
        protected SitemapService $sitemapService
    ) {}

    /**
     * Main sitemap index
     */
    public function index(): Response
    {
        $xml = $this->sitemapService->generateSitemapIndex();

        return $this->xmlResponse($xml);
    }

    /**
     * Static pages sitemap
     */
    public function static(): Response
    {
        $xml = $this->sitemapService->generateStaticSitemap();

        return $this->xmlResponse($xml);
    }

    /**
     * Provinces sitemap
     */
    public function provinces(): Response
    {
        $xml = $this->sitemapService->generateProvincesSitemap();

        return $this->xmlResponse($xml);
    }

    /**
     * Day of week sitemap
     */
    public function days(): Response
    {
        $xml = $this->sitemapService->generateDaysSitemap();

        return $this->xmlResponse($xml);
    }

    /**
     * Vietlott sitemap
     */
    public function vietlott(): Response
    {
        $xml = $this->sitemapService->generateVietlottSitemap();

        return $this->xmlResponse($xml);
    }

    /**
     * Monthly results sitemap
     */
    public function results(string $yearMonth): Response
    {
        $xml = $this->sitemapService->generateResultsSitemap($yearMonth);

        return $this->xmlResponse($xml);
    }

    /**
     * Return XML response with proper headers
     */
    protected function xmlResponse(string $xml): Response
    {
        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
