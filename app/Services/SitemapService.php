<?php

namespace App\Services;

use App\Models\LotteryResult;
use App\Models\Prediction;
use App\Models\Province;
use App\Models\VietlottResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    private const CACHE_TTL_STATIC = 86400;  // 24 hours
    private const CACHE_TTL_DYNAMIC = 21600; // 6 hours

    /**
     * Get base URL for sitemap
     */
    protected function getBaseUrl(): string
    {
        return rtrim(config('app.url'), '/');
    }

    /**
     * Generate the main sitemap index XML
     */
    public function generateSitemapIndex(): string
    {
        return Cache::remember('sitemap_index', self::CACHE_TTL_STATIC, function () {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // Static sitemap
            $xml .= $this->buildSitemapEntry("{$baseUrl}/sitemap-static.xml", $now);

            // Province sitemap
            $xml .= $this->buildSitemapEntry("{$baseUrl}/sitemap-provinces.xml", $now);

            // Days sitemap
            $xml .= $this->buildSitemapEntry("{$baseUrl}/sitemap-days.xml", $now);

            // Vietlott sitemap
            $xml .= $this->buildSitemapEntry("{$baseUrl}/sitemap-vietlott.xml", $now);

            // Predictions sitemap
            $xml .= $this->buildSitemapEntry("{$baseUrl}/sitemap-predictions.xml", $now);

            // Monthly results sitemaps (rolling 2 months)
            $currentMonth = Carbon::now();
            for ($i = 0; $i < 2; $i++) {
                $month = $currentMonth->copy()->subMonths($i);
                $yearMonth = $month->format('Y-m');
                $xml .= $this->buildSitemapEntry("{$baseUrl}/sitemap-results-{$yearMonth}.xml", $now);
            }

            $xml .= '</sitemapindex>';

            return $xml;
        });
    }

    /**
     * Build a single sitemap entry for the index
     */
    protected function buildSitemapEntry(string $loc, string $lastmod): string
    {
        return "  <sitemap>\n    <loc>{$loc}</loc>\n    <lastmod>{$lastmod}</lastmod>\n  </sitemap>\n";
    }

    /**
     * Generate static pages sitemap
     */
    public function generateStaticSitemap(): string
    {
        return Cache::remember('sitemap_static', self::CACHE_TTL_STATIC, function () {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            $urls = [
                // Homepage
                ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],

                // Regional pages
                ['loc' => '/xsmb', 'priority' => '0.9', 'changefreq' => 'daily'],
                ['loc' => '/xsmt', 'priority' => '0.9', 'changefreq' => 'daily'],
                ['loc' => '/xsmn', 'priority' => '0.9', 'changefreq' => 'daily'],

                // Results book
                ['loc' => '/so-ket-qua', 'priority' => '0.8', 'changefreq' => 'daily'],

                // Statistics pages
                ['loc' => '/thong-ke', 'priority' => '0.7', 'changefreq' => 'weekly'],
                ['loc' => '/thong-ke/tan-suat-loto', 'priority' => '0.7', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/loto-gan', 'priority' => '0.7', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/dau-duoi-loto', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/thong-ke-nhanh', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/theo-tong', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/quan-trong', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/dac-biet-tuan', 'priority' => '0.6', 'changefreq' => 'weekly'],
                ['loc' => '/thong-ke/dac-biet-thang', 'priority' => '0.6', 'changefreq' => 'monthly'],
                ['loc' => '/thong-ke/cang-loto', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/chu-ky-gan-theo-tinh', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/chu-ky-dan-loto', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/chu-ky-dac-biet', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/dai-nhat', 'priority' => '0.6', 'changefreq' => 'daily'],
                ['loc' => '/thong-ke/tan-so-nhip-loto', 'priority' => '0.6', 'changefreq' => 'daily'],

                // Feature pages
                ['loc' => '/do-ve-so', 'priority' => '0.5', 'changefreq' => 'weekly'],
                ['loc' => '/lich-mo-thuong', 'priority' => '0.5', 'changefreq' => 'weekly'],
                ['loc' => '/quay-thu-xo-so-hom-nay', 'priority' => '0.5', 'changefreq' => 'daily'],
                ['loc' => '/quay-thu-xsmb', 'priority' => '0.5', 'changefreq' => 'daily'],
                ['loc' => '/quay-thu-xsmt', 'priority' => '0.5', 'changefreq' => 'daily'],
                ['loc' => '/quay-thu-xsmn', 'priority' => '0.5', 'changefreq' => 'daily'],

                // RSS
                ['loc' => '/rss', 'priority' => '0.3', 'changefreq' => 'weekly'],

                // Prediction pages
                ['loc' => '/du-doan', 'priority' => '0.8', 'changefreq' => 'daily'],
                ['loc' => '/du-doan/xsmb', 'priority' => '0.8', 'changefreq' => 'daily'],
                ['loc' => '/du-doan/xsmt', 'priority' => '0.8', 'changefreq' => 'daily'],
                ['loc' => '/du-doan/xsmn', 'priority' => '0.8', 'changefreq' => 'daily'],
            ];

            return $this->buildUrlsetXml($urls, $baseUrl, $now);
        });
    }

    /**
     * Generate predictions sitemap
     */
    public function generatePredictionsSitemap(): string
    {
        return Cache::remember('sitemap_predictions', self::CACHE_TTL_DYNAMIC, function () {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            $predictions = Prediction::where('status', 'published')
                ->orderBy('prediction_date', 'desc')
                ->take(100) // Limit to recent 100 predictions per region
                ->get();

            $regionSlugs = [
                'xsmb' => 'mien-bac',
                'xsmt' => 'mien-trung',
                'xsmn' => 'mien-nam',
            ];

            $urls = [];
            foreach ($predictions as $prediction) {
                $regionSlug = $prediction->region_slug;
                $dateSlug = $prediction->date_slug;
                $regionFullSlug = $regionSlugs[$regionSlug] ?? $regionSlug;

                $urls[] = [
                    'loc' => "/du-doan/du-doan-{$regionSlug}-{$dateSlug}-soi-cau-xo-so-{$regionFullSlug}-{$dateSlug}.html",
                    'priority' => '0.7',
                    'changefreq' => 'never',
                    'lastmod' => $prediction->published_at?->toW3cString() ?? $now,
                ];
            }

            return $this->buildUrlsetXml($urls, $baseUrl, $now);
        });
    }

    /**
     * Generate provinces sitemap
     */
    public function generateProvincesSitemap(): string
    {
        return Cache::remember('sitemap_provinces', self::CACHE_TTL_STATIC, function () {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            $provinces = Province::where('is_active', true)
                ->orderBy('region')
                ->orderBy('sort_order')
                ->get();

            $urls = [];
            foreach ($provinces as $province) {
                $regionPath = $this->getRegionPath($province->region);
                $urls[] = [
                    'loc' => "/{$regionPath}/{$province->slug}",
                    'priority' => '0.8',
                    'changefreq' => 'daily',
                ];
            }

            return $this->buildUrlsetXml($urls, $baseUrl, $now);
        });
    }

    /**
     * Generate days of week sitemap (21 URLs: 3 regions x 7 days)
     */
    public function generateDaysSitemap(): string
    {
        return Cache::remember('sitemap_days', self::CACHE_TTL_STATIC, function () {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            $regions = ['xsmb', 'xsmt', 'xsmn'];
            $days = ['thu-2', 'thu-3', 'thu-4', 'thu-5', 'thu-6', 'thu-7', 'chu-nhat'];

            $urls = [];
            foreach ($regions as $region) {
                foreach ($days as $day) {
                    $urls[] = [
                        'loc' => "/{$region}/{$day}",
                        'priority' => '0.7',
                        'changefreq' => 'weekly',
                    ];
                }
            }

            return $this->buildUrlsetXml($urls, $baseUrl, $now);
        });
    }

    /**
     * Generate Vietlott sitemap
     */
    public function generateVietlottSitemap(): string
    {
        return Cache::remember('sitemap_vietlott', self::CACHE_TTL_STATIC, function () {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            $urls = [
                ['loc' => '/xo-so-vietlott', 'priority' => '0.8', 'changefreq' => 'daily'],
                ['loc' => '/xo-so-vietlott/mega-645', 'priority' => '0.7', 'changefreq' => 'daily'],
                ['loc' => '/xo-so-vietlott/power-655', 'priority' => '0.7', 'changefreq' => 'daily'],
                ['loc' => '/xo-so-vietlott/max-3d', 'priority' => '0.7', 'changefreq' => 'daily'],
                ['loc' => '/xo-so-vietlott/max-3d-pro', 'priority' => '0.7', 'changefreq' => 'daily'],
            ];

            return $this->buildUrlsetXml($urls, $baseUrl, $now);
        });
    }

    /**
     * Generate monthly results sitemap
     */
    public function generateResultsSitemap(string $yearMonth): string
    {
        $cacheKey = "sitemap_results_{$yearMonth}";

        return Cache::remember($cacheKey, self::CACHE_TTL_DYNAMIC, function () use ($yearMonth) {
            $baseUrl = $this->getBaseUrl();
            $now = Carbon::now()->toW3cString();

            // Parse year-month
            try {
                $date = Carbon::createFromFormat('Y-m', $yearMonth);
            } catch (\Exception $e) {
                return $this->buildUrlsetXml([], $baseUrl, $now);
            }

            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();

            // Get all unique draw dates for the month
            $results = LotteryResult::whereBetween('draw_date', [$startDate, $endDate])
                ->whereHas('province', fn($q) => $q->where('is_active', true))
                ->with('province')
                ->orderBy('draw_date', 'desc')
                ->get();

            // Group by region and date
            $urls = [];
            $processedDates = [];

            foreach ($results as $result) {
                $regionPath = $this->getRegionPath($result->province->region);
                $dateStr = $result->draw_date->format('d-m-Y');
                $key = "{$regionPath}_{$dateStr}";

                if (!isset($processedDates[$key])) {
                    $processedDates[$key] = true;
                    $urls[] = [
                        'loc' => "/{$regionPath}/{$dateStr}",
                        'priority' => '0.6',
                        'changefreq' => 'never',
                        'lastmod' => $result->updated_at?->toW3cString() ?? $now,
                    ];
                }
            }

            return $this->buildUrlsetXml($urls, $baseUrl, $now);
        });
    }

    /**
     * Build URL set XML
     */
    protected function buildUrlsetXml(array $urls, string $baseUrl, string $defaultLastmod): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>{$baseUrl}{$url['loc']}</loc>\n";
            $xml .= "    <lastmod>" . ($url['lastmod'] ?? $defaultLastmod) . "</lastmod>\n";
            $xml .= "    <changefreq>{$url['changefreq']}</changefreq>\n";
            $xml .= "    <priority>{$url['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get URL path for region
     */
    protected function getRegionPath(string $region): string
    {
        return match ($region) {
            'south' => 'xsmn',
            'central' => 'xsmt',
            'north' => 'xsmb',
            default => 'xsmn',
        };
    }

    /**
     * Clear all sitemap caches
     */
    public function clearAllCaches(): void
    {
        Cache::forget('sitemap_index');
        Cache::forget('sitemap_static');
        Cache::forget('sitemap_provinces');
        Cache::forget('sitemap_days');
        Cache::forget('sitemap_vietlott');
        Cache::forget('sitemap_predictions');
        $this->clearResultsCaches();
    }

    /**
     * Clear province-related sitemap caches
     */
    public function clearProvinceCaches(): void
    {
        Cache::forget('sitemap_index');
        Cache::forget('sitemap_provinces');
    }

    /**
     * Clear results sitemap caches (rolling 2 months)
     */
    public function clearResultsCaches(): void
    {
        $currentMonth = Carbon::now();
        for ($i = 0; $i < 2; $i++) {
            $month = $currentMonth->copy()->subMonths($i);
            $yearMonth = $month->format('Y-m');
            Cache::forget("sitemap_results_{$yearMonth}");
        }
    }
}
