<?php

namespace App\Services;

use App\Models\LotteryResult;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class RssFeedService
{
    /**
     * RSS code to database code mapping
     */
    protected array $rssToDbMapping = [
        // South (XSMN)
        'AG' => 'ag',
        'BL' => 'bl',
        'BP' => 'bp',
        'BD' => 'bdu',
        'BTH' => 'bith',
        'BT' => 'bt',
        'CM' => 'cm',
        'CT' => 'ct',
        'DN' => 'dni',
        'DT' => 'dt',
        'HG' => 'hg',
        'HCM' => 'tphc',
        'KG' => 'kg',
        'LA' => 'la',
        'DL' => 'dalat',
        'ST' => 'st',
        'TN' => 'tn',
        'TG' => 'tg',
        'TV' => 'tv',
        'VL' => 'vl',
        'VT' => 'vt',

        // Central (XSMT)
        'BDIN' => 'bdi',
        'DNG' => 'dna',
        'DLK' => 'dl',
        'DNO' => 'dno',
        'GL' => 'gl',
        'KH' => 'kh',
        'KT' => 'kt',
        'NT' => 'nt',
        'PY' => 'py',
        'QB' => 'qb',
        'QNG' => 'qng',
        'QNM' => 'qna',
        'QT' => 'qt',
        'TTH' => 'th',

        // North (XSMB)
        'MB' => 'hn',
    ];

    /**
     * Get province by RSS code
     */
    public function getProvinceByRssCode(string $rssCode): ?Province
    {
        $rssCode = strtoupper($rssCode);
        $dbCode = $this->rssToDbMapping[$rssCode] ?? null;

        if (!$dbCode) {
            return null;
        }

        return Province::where('code', $dbCode)->where('is_active', true)->first();
    }

    /**
     * Get RSS code from province
     */
    public function getRssCode(Province $province): ?string
    {
        $flipped = array_flip($this->rssToDbMapping);
        return $flipped[$province->code] ?? null;
    }

    /**
     * Get all provinces with their RSS codes grouped by region
     */
    public function getProvincesWithRssCodes(): array
    {
        return Cache::remember('rss_provinces_grouped', 300, function () {
            $provinces = Province::where('is_active', true)
                ->orderBy('region')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();

            $grouped = [
                'north' => [],
                'central' => [],
                'south' => [],
            ];

            foreach ($provinces as $province) {
                $rssCode = $this->getRssCode($province);
                if ($rssCode) {
                    $grouped[$province->region][] = [
                        'province' => $province,
                        'rss_code' => $rssCode,
                    ];
                }
            }

            return $grouped;
        });
    }

    /**
     * Generate regional RSS feed (grouped by date)
     */
    public function generateRegionalFeed(string $region, string $regionCode, string $regionName): string
    {
        $cacheKey = "rss_feed_{$regionCode}";

        return Cache::remember($cacheKey, 300, function () use ($region, $regionCode, $regionName) {
            $dbRegion = $this->mapRegionCode($region);

            $results = LotteryResult::whereHas('province', function ($query) use ($dbRegion) {
                $query->where('region', $dbRegion)->where('is_active', true);
            })
                ->with('province')
                ->orderBy('draw_date', 'desc')
                ->orderBy('province_id')
                ->limit(100)
                ->get();

            // Group results by date
            $groupedByDate = $results->groupBy(fn($r) => $r->draw_date->format('Y-m-d'));

            return $this->buildRegionalRssFeed($regionName, $regionCode, $dbRegion, $groupedByDate);
        });
    }

    /**
     * Generate province RSS feed
     */
    public function generateProvinceFeed(Province $province): string
    {
        $cacheKey = "rss_feed_province_{$province->code}";

        return Cache::remember($cacheKey, 300, function () use ($province) {
            $results = LotteryResult::where('province_id', $province->id)
                ->with('province')
                ->orderBy('draw_date', 'desc')
                ->limit(30)
                ->get();

            $rssCode = $this->getRssCode($province);
            return $this->buildProvinceFeedXml($province->name, $rssCode, $results);
        });
    }

    /**
     * Build regional RSS XML feed (grouped by date)
     */
    protected function buildRegionalRssFeed(string $name, string $code, string $region, Collection $groupedByDate): string
    {
        $siteUrl = config('app.url');
        $now = Carbon::now()->toRfc2822String();
        $regionPath = $this->getRegionPath($region);
        $regionFullNames = [
            'north' => 'xo-so-mien-bac',
            'central' => 'xo-so-mien-trung',
            'south' => 'xo-so-mien-nam',
        ];
        $regionFullName = $regionFullNames[$region] ?? 'xo-so-mien-nam';

        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<rss version="2.0">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= "  <title>Kết quả xổ số {$name}</title>\n";
        $xml .= "  <description>Kết quả xổ số {$name} hàng ngày</description>\n";
        $xml .= "  <link>{$siteUrl}/{$regionPath}-{$regionFullName}.html</link>\n";
        $xml .= "  <copyright>Copyright " . date('Y') . "</copyright>\n";
        $xml .= "  <pubDate>{$now}</pubDate>\n";
        $xml .= "  <lastBuildDate>{$now}</lastBuildDate>\n";
        $xml .= "  <generator>{$siteUrl}</generator>\n";

        foreach ($groupedByDate->take(10) as $dateStr => $results) {
            $xml .= $this->buildRegionalRssItem($results, $name, $region);
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    /**
     * Build RSS XML feed for province
     */
    protected function buildProvinceFeedXml(string $name, string $code, Collection $results): string
    {
        $siteUrl = config('app.url');
        $now = Carbon::now()->toRfc2822String();

        $xml = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $xml .= '<rss version="2.0">' . "\n";
        $xml .= '<channel>' . "\n";
        $xml .= "  <title>Kết quả xổ số {$name}</title>\n";
        $xml .= "  <description>Kết quả xổ số {$name} hàng ngày</description>\n";
        $xml .= "  <link>{$siteUrl}</link>\n";
        $xml .= "  <copyright>Copyright " . date('Y') . "</copyright>\n";
        $xml .= "  <pubDate>{$now}</pubDate>\n";
        $xml .= "  <lastBuildDate>{$now}</lastBuildDate>\n";
        $xml .= "  <generator>{$siteUrl}</generator>\n";

        foreach ($results as $result) {
            $xml .= $this->buildProvinceRssItem($result);
        }

        $xml .= '</channel>' . "\n";
        $xml .= '</rss>';

        return $xml;
    }

    /**
     * Build RSS item for regional feed (multiple provinces in one day)
     */
    protected function buildRegionalRssItem(Collection $results, string $regionName, string $region): string
    {
        $siteUrl = config('app.url');
        $firstResult = $results->first();
        $date = Carbon::parse($firstResult->draw_date);
        $regionPath = $this->getRegionPath($region);
        $regionFullNames = [
            'north' => 'xo-so-mien-bac',
            'central' => 'xo-so-mien-trung',
            'south' => 'xo-so-mien-nam',
        ];
        $regionFullName = $regionFullNames[$region] ?? 'xo-so-mien-nam';

        $title = "KẾT QUẢ XỔ SỐ " . mb_strtoupper($regionName) . " NGÀY " . $date->format('d/m/Y');
        $link = "{$siteUrl}/{$regionPath}-{$regionFullName}-{$date->format('d-m-Y')}.html";
        $pubDate = $date->startOfDay()->toRfc2822String();

        // Build description with all provinces
        $descriptionParts = [];
        foreach ($results as $result) {
            $descriptionParts[] = $this->formatProvinceResult($result, $region);
        }
        $description = implode("\n", $descriptionParts);

        $xml = "  <item>\n";
        $xml .= "    <title>" . htmlspecialchars($title, ENT_XML1) . "</title>\n";
        $xml .= "    <description>" . htmlspecialchars($description, ENT_XML1) . "</description>\n";
        $xml .= "    <link>{$link}</link>\n";
        $xml .= "    <pubDate>{$pubDate}</pubDate>\n";
        $xml .= "  </item>\n";

        return $xml;
    }

    /**
     * Build RSS item for province feed (single province)
     */
    protected function buildProvinceRssItem(LotteryResult $result): string
    {
        $siteUrl = config('app.url');
        $province = $result->province;
        $date = Carbon::parse($result->draw_date);
        $regionPath = $this->getRegionPath($province->region);
        $regionFullNames = [
            'north' => 'xo-so-mien-bac',
            'central' => 'xo-so-mien-trung',
            'south' => 'xo-so-mien-nam',
        ];
        $regionFullName = $regionFullNames[$province->region] ?? 'xo-so-mien-nam';

        $title = "KẾT QUẢ XỔ SỐ " . mb_strtoupper($province->name) . " NGÀY " . $date->format('d/m/Y');
        $link = "{$siteUrl}/{$regionPath}-{$regionFullName}-{$date->format('d-m-Y')}.html";
        $pubDate = $date->startOfDay()->toRfc2822String();
        $description = $this->formatProvinceResult($result, $province->region);

        $xml = "  <item>\n";
        $xml .= "    <title>" . htmlspecialchars($title, ENT_XML1) . "</title>\n";
        $xml .= "    <description>" . htmlspecialchars($description, ENT_XML1) . "</description>\n";
        $xml .= "    <link>{$link}</link>\n";
        $xml .= "    <pubDate>{$pubDate}</pubDate>\n";
        $xml .= "  </item>\n";

        return $xml;
    }

    /**
     * Format province result for RSS description
     */
    protected function formatProvinceResult(LotteryResult $result, string $region): string
    {
        $province = $result->province;
        $lines = [];
        $lines[] = "[{$province->name}]";

        if ($region === 'north') {
            $lines[] = "ĐB: " . $this->formatPrize($result->prize_special);
            $lines[] = "1: " . $this->formatPrize($result->prize_1);
            $lines[] = "2: " . $this->formatPrize($result->prize_2);
            $lines[] = "3: " . $this->formatPrize($result->prize_3);
            $lines[] = "4: " . $this->formatPrize($result->prize_4);
            $lines[] = "5: " . $this->formatPrize($result->prize_5);
            $lines[] = "6: " . $this->formatPrize($result->prize_6);
            $lines[] = "7: " . $this->formatPrize($result->prize_7);
        } else {
            $lines[] = "ĐB: " . $this->formatPrize($result->prize_special);
            $lines[] = "1: " . $this->formatPrize($result->prize_1);
            $lines[] = "2: " . $this->formatPrize($result->prize_2);
            $lines[] = "3: " . $this->formatPrize($result->prize_3);
            $lines[] = "4: " . $this->formatPrize($result->prize_4);
            $lines[] = "5: " . $this->formatPrize($result->prize_5);
            $lines[] = "6: " . $this->formatPrize($result->prize_6);
            $lines[] = "7: " . $this->formatPrize($result->prize_7);
            $lines[] = "8: " . $this->formatPrize($result->prize_8);
        }

        return implode("\n", $lines);
    }

    /**
     * Format prize value (comma-separated to dash-separated)
     */
    protected function formatPrize(?string $prize): string
    {
        if (empty($prize)) {
            return '---';
        }

        $values = array_map('trim', explode(',', $prize));
        return implode('-', $values);
    }

    /**
     * Map region code to database region
     */
    protected function mapRegionCode(string $region): string
    {
        return match ($region) {
            'xsmn' => 'south',
            'xsmt' => 'central',
            'xsmb' => 'north',
            default => $region,
        };
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
}
