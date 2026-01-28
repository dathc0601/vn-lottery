<?php

namespace App\Http\Controllers;

use App\Services\RssFeedService;
use Illuminate\Http\Response;

class RssFeedController extends Controller
{
    public function __construct(
        protected RssFeedService $rssFeedService
    ) {}

    /**
     * RSS index page listing all available feeds
     */
    public function index()
    {
        $provincesGrouped = $this->rssFeedService->getProvincesWithRssCodes();

        return view('rss.index', compact('provincesGrouped'));
    }

    /**
     * XSMN (South) regional feed
     */
    public function xsmn(): Response
    {
        $xml = $this->rssFeedService->generateRegionalFeed('xsmn', 'MN', 'Miền Nam');

        return $this->xmlResponse($xml);
    }

    /**
     * XSMT (Central) regional feed
     */
    public function xsmt(): Response
    {
        $xml = $this->rssFeedService->generateRegionalFeed('xsmt', 'MT', 'Miền Trung');

        return $this->xmlResponse($xml);
    }

    /**
     * XSMB (North) regional feed
     */
    public function xsmb(): Response
    {
        $xml = $this->rssFeedService->generateRegionalFeed('xsmb', 'MB', 'Miền Bắc');

        return $this->xmlResponse($xml);
    }

    /**
     * Province-specific feed
     */
    public function province(string $code): Response
    {
        $province = $this->rssFeedService->getProvinceByRssCode($code);

        if (!$province) {
            abort(404, 'Không tìm thấy tỉnh thành');
        }

        $xml = $this->rssFeedService->generateProvinceFeed($province);

        return $this->xmlResponse($xml);
    }

    /**
     * Return XML response with proper headers
     */
    protected function xmlResponse(string $xml): Response
    {
        return response($xml, 200)
            ->header('Content-Type', 'application/rss+xml; charset=utf-8');
    }
}
