<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\LotteryResult;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LotteryApiService
{
    protected string $xsmbGithubUrl = 'https://raw.githubusercontent.com/khiemdoan/vietnam-lottery-xsmb-analysis/refs/heads/main/data/xsmb.csv';
    protected string $xsmtRssUrl = 'https://xskt.com.vn/rss-feed/mien-trung-xsmt.rss';
    protected string $xsmnRssUrl = 'https://xskt.com.vn/rss-feed/mien-nam-xsmn.rss';
    protected int $timeout = 30; // seconds

    protected const XSMT_PROVINCE_MAP = [
        'Phú Yên'           => 'phye',
        'Thừa Thiên Huế'    => 'thth',
        'Khánh Hòa'         => 'khho',
        'Kon Tum'            => 'kotu',
        'Đà Nẵng'           => 'dana',
        'Đắk Nông'          => 'dano',
        'Quảng Ngãi'         => 'qung',
        'Bình Định'          => 'bidi',
        'Quảng Bình'         => 'qubi',
        'Quảng Trị'          => 'qutr',
        'Quảng Nam'           => 'quna',
        'Đắk Lắk'           => 'dalak',
        'Ninh Thuận'         => 'nith',
        'Gia Lai'            => 'gila',
    ];

    protected const XSMN_PROVINCE_MAP = [
        'Hậu Giang'     => 'hagi',
        'Hồ Chí Minh'   => 'tphc',
        'Bình Phước'     => 'biph',
        'Long An'        => 'loan',
        'Bình Dương'     => 'bidu',
        'Trà Vinh'       => 'trvi',
        'Vĩnh Long'      => 'vilo',
        'Bình Thuận'     => 'bith',
        'Tây Ninh'       => 'tani',
        'An Giang'       => 'angi',
        'Sóc Trăng'      => 'sotr',
        'Đồng Nai'       => 'dona',
        'Cần Thơ'        => 'cath',
        'Vũng Tàu'       => 'vuta',
        'Bạc Liêu'       => 'bali',
        'Bến Tre'        => 'betre',
        'Cà Mau'         => 'cama',
        'Đồng Tháp'      => 'doth',
        'Lâm Đồng'       => 'dalat',   // DB name: "Đà Lạt"
        'Tiền Giang'     => 'tigi',
        'Kiên Giang'     => 'kigi',
    ];

    /**
     * RSS feeds may use variant spellings for some province names.
     * Map them to the canonical names used in XSMT/XSMN_PROVINCE_MAP.
     */
    protected const PROVINCE_NAME_ALIASES = [
        'Đắc Lắc'  => 'Đắk Lắk',
        'Đắk Lăk'  => 'Đắk Lắk',
        'Đắc Nông' => 'Đắk Nông',
    ];

    /**
     * Log API request to database
     */
    protected function logApiRequest(
        string $endpoint,
        ?string $provinceCode,
        ?int $responseStatus,
        ?int $responseTime,
        ?string $errorMessage,
        int $fetchedCount
    ): void {
        try {
            ApiLog::create([
                'endpoint' => $endpoint,
                'province_code' => $provinceCode,
                'response_status' => $responseStatus,
                'response_time_ms' => $responseTime,
                'error_message' => $errorMessage,
                'fetched_count' => $fetchedCount,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log API request', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // RSS Feed: Generic helpers (shared by XSMT and XSMN)
    // =========================================================================

    /**
     * Fetch an RSS feed with caching
     *
     * @param string $url RSS feed URL
     * @param string $cacheKey Cache key for this feed
     * @param string $logCode Province code for logging (e.g. 'xsmt', 'xsmn')
     * @return array [date => [provinceName => prizes]]
     */
    protected function fetchRssFeed(string $url, string $cacheKey, string $logCode): array
    {
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)->get($url);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiRequest(
                $url,
                $logCode,
                $response->status(),
                $responseTime,
                $response->successful() ? null : 'RSS fetch failed',
                0
            );

            if (!$response->successful()) {
                return [];
            }

            $result = $this->parseRss($response->body());

            if (!empty($result)) {
                Cache::put($cacheKey, $result, 5 * 60);
            }

            return $result;

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiRequest(
                $url,
                $logCode,
                0,
                $responseTime,
                $e->getMessage(),
                0
            );

            Log::error("Failed to fetch {$logCode} RSS feed", [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Fetch XSMT RSS feed with 5-minute caching
     *
     * @return array [date => [provinceName => prizes]]
     */
    public function fetchXSMTRssFeed(): array
    {
        return $this->fetchRssFeed($this->xsmtRssUrl, 'xsmt_rss_feed', 'xsmt');
    }

    /**
     * Fetch XSMN RSS feed with 5-minute caching
     *
     * @return array [date => [provinceName => prizes]]
     */
    public function fetchXSMNRssFeed(): array
    {
        return $this->fetchRssFeed($this->xsmnRssUrl, 'xsmn_rss_feed', 'xsmn');
    }

    /**
     * Parse RSS XML into structured data
     *
     * @param string $xml
     * @return array [date => [provinceName => prizes]]
     */
    protected function parseRss(string $xml): array
    {
        $results = [];

        try {
            $feed = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NONET);

            if (!$feed || !isset($feed->channel->item)) {
                return [];
            }

            foreach ($feed->channel->item as $item) {
                $link = (string) $item->link;
                $description = (string) $item->description;

                $date = $this->parseDateFromRssLink($link);
                if (!$date) {
                    continue;
                }

                $provinces = $this->parseRssDescription($description);
                if (!empty($provinces)) {
                    $results[$date] = $provinces;
                }
            }

        } catch (\Exception $e) {
            Log::error('Failed to parse RSS XML', [
                'error' => $e->getMessage(),
            ]);
        }

        return $results;
    }

    /**
     * Parse date from RSS item link URL
     *
     * @param string $link e.g. "https://xskt.com.vn/...ngay-27-1-2026..."
     * @return string|null Y-m-d date string
     */
    protected function parseDateFromRssLink(string $link): ?string
    {
        if (preg_match('/ngay-(\d{1,2})-(\d{1,2})-(\d{4})/', $link, $matches)) {
            $day = str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            $month = str_pad($matches[2], 2, '0', STR_PAD_LEFT);
            $year = $matches[3];

            return "{$year}-{$month}-{$day}";
        }

        return null;
    }

    /**
     * Parse RSS description into province blocks
     *
     * @param string $description HTML description from RSS item
     * @return array [provinceName => prizes]
     */
    protected function parseRssDescription(string $description): array
    {
        $text = html_entity_decode(strip_tags($description));
        $provinces = [];

        // Split by province name markers [Province Name]
        $parts = preg_split('/\[([^\]]+)\]/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        // parts[0] is text before first bracket (usually empty)
        // parts[1] = province name, parts[2] = prize content, etc.
        for ($i = 1; $i < count($parts) - 1; $i += 2) {
            $provinceName = trim($parts[$i]);
            $provinceName = self::PROVINCE_NAME_ALIASES[$provinceName] ?? $provinceName;
            $content = $parts[$i + 1] ?? '';

            $prizes = $this->parseRssProvinceBlock($content);
            if ($prizes) {
                $provinces[$provinceName] = $prizes;
            }
        }

        return $provinces;
    }

    /**
     * Parse a single province's prize block from RSS
     *
     * @param string $content Prize text for one province
     * @return array|null Prize data array or null on failure
     */
    protected function parseRssProvinceBlock(string $content): ?array
    {
        $prizes = [];
        $lines = preg_split('/\r?\n/', trim($content));

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Special prize: "ĐB: 243372"
            if (preg_match('/^ĐB:\s*(\d+)/', $line, $m)) {
                $prizes['prize_special'] = $m[1];
                continue;
            }

            // Concatenated G7+G8 line: "7: 3008: 68" → G7=300, G8=68
            if (preg_match('/^7:\s*(\d{3})8:\s*(\d{2})/', $line, $m)) {
                $prizes['prize_7'] = $m[1];
                $prizes['prize_8'] = $m[2];
                continue;
            }

            // Regular prize lines: "N: value" or "N: val1 - val2 - val3"
            if (preg_match('/^(\d):\s*(.+)/', $line, $m)) {
                $prizeNum = $m[1];
                $values = $m[2];

                // Multi-value: split on " - " and join with comma
                if (strpos($values, ' - ') !== false) {
                    $parts = array_map('trim', explode(' - ', $values));
                    $prizes["prize_{$prizeNum}"] = implode(',', $parts);
                } else {
                    $prizes["prize_{$prizeNum}"] = trim($values);
                }
            }
        }

        if (empty($prizes) || !isset($prizes['prize_special'])) {
            return null;
        }

        return $prizes;
    }

    /**
     * Store a single RSS result
     *
     * @param Province $province
     * @param string $date Y-m-d format
     * @param array $prizes Prize data from parseRssProvinceBlock
     * @param string $drawTime HH:MM:SS format
     * @return bool
     */
    protected function storeRssResult(Province $province, string $date, array $prizes, string $drawTime): bool
    {
        try {
            $drawDate = Carbon::parse($date);
            $turnNum = $drawDate->format('d/m/Y');

            $timeParts = explode(':', $drawTime);
            $hour = (int) $timeParts[0];
            $minute = (int) $timeParts[1];

            $detail = [
                $prizes['prize_special'] ?? null,
                $prizes['prize_1'] ?? null,
                $prizes['prize_2'] ?? null,
                $prizes['prize_3'] ?? null,
                $prizes['prize_4'] ?? null,
                $prizes['prize_5'] ?? null,
                $prizes['prize_6'] ?? null,
                $prizes['prize_7'] ?? null,
                $prizes['prize_8'] ?? null,
            ];

            LotteryResult::updateOrCreate(
                [
                    'province_id' => $province->id,
                    'turn_num' => $turnNum,
                ],
                [
                    'draw_date' => $date,
                    'draw_time' => $drawTime,
                    'draw_timestamp' => $drawDate->setTime($hour, $minute)->timestamp,
                    'open_num' => null,
                    'prize_special' => $prizes['prize_special'] ?? null,
                    'prize_1' => $prizes['prize_1'] ?? null,
                    'prize_2' => $prizes['prize_2'] ?? null,
                    'prize_3' => $prizes['prize_3'] ?? null,
                    'prize_4' => $prizes['prize_4'] ?? null,
                    'prize_5' => $prizes['prize_5'] ?? null,
                    'prize_6' => $prizes['prize_6'] ?? null,
                    'prize_7' => $prizes['prize_7'] ?? null,
                    'prize_8' => $prizes['prize_8'] ?? null,
                    'detail_json' => $detail,
                    'status' => 0,
                ]
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to store RSS result', [
                'province_id' => $province->id,
                'date' => $date,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Fetch and store results from an RSS feed for a province
     *
     * @param Province $province
     * @param array $feedData Parsed RSS data [date => [provinceName => prizes]]
     * @param array $provinceMap Province name → code mapping
     * @param string $drawTime HH:MM:SS format
     * @param int $limitNum Number of latest results to store
     * @return int Number of results stored
     */
    protected function fetchAndStoreFromRss(
        Province $province,
        array $feedData,
        array $provinceMap,
        string $drawTime,
        int $limitNum
    ): int {
        $provinceName = array_search($province->code, $provinceMap);

        if ($provinceName === false) {
            Log::warning('Province code not found in RSS map', [
                'province_code' => $province->code,
            ]);
            return 0;
        }

        if (empty($feedData)) {
            return 0;
        }

        // Sort dates descending (most recent first)
        krsort($feedData);

        $stored = 0;
        foreach ($feedData as $date => $provinces) {
            if ($stored >= $limitNum) {
                break;
            }

            if (isset($provinces[$provinceName])) {
                if ($this->storeRssResult($province, $date, $provinces[$provinceName], $drawTime)) {
                    $stored++;
                }
            }
        }

        Log::info('RSS results fetched', [
            'province' => $province->code,
            'stored' => $stored,
            'limit' => $limitNum,
        ]);

        return $stored;
    }

    /**
     * Fetch and store XSMT results from RSS feed for a province
     *
     * @param Province $province Must be a central province
     * @param int $limitNum Number of latest results to store
     * @return int Number of results stored
     */
    public function fetchAndStoreXSMTResults(Province $province, int $limitNum = 1): int
    {
        $feedData = $this->fetchXSMTRssFeed();
        return $this->fetchAndStoreFromRss($province, $feedData, self::XSMT_PROVINCE_MAP, '17:15:00', $limitNum);
    }

    /**
     * Fetch and store XSMN results from RSS feed for a province
     *
     * @param Province $province Must be a south province
     * @param int $limitNum Number of latest results to store
     * @return int Number of results stored
     */
    public function fetchAndStoreXSMNResults(Province $province, int $limitNum = 1): int
    {
        $feedData = $this->fetchXSMNRssFeed();
        return $this->fetchAndStoreFromRss($province, $feedData, self::XSMN_PROVINCE_MAP, '16:15:00', $limitNum);
    }

    /**
     * Fetch missing results from an RSS feed for a province
     *
     * @param Province $province
     * @param array $missingDates Array of missing date strings (Y-m-d format)
     * @param array $feedData Parsed RSS data [date => [provinceName => prizes]]
     * @param array $provinceMap Province name → code mapping
     * @param string $drawTime HH:MM:SS format
     * @return int Number of results stored
     */
    protected function fetchMissingFromRss(
        Province $province,
        array $missingDates,
        array $feedData,
        array $provinceMap,
        string $drawTime
    ): int {
        if (empty($missingDates)) {
            return 0;
        }

        $provinceName = array_search($province->code, $provinceMap);

        if ($provinceName === false) {
            Log::warning('Province code not found in RSS map for fill-gaps', [
                'province_code' => $province->code,
            ]);
            return 0;
        }

        Log::info('Attempting to fetch missing results from RSS', [
            'province' => $province->code,
            'missing_dates' => $missingDates,
        ]);

        if (empty($feedData)) {
            Log::warning('No RSS data available for missing dates');
            return 0;
        }

        $stored = 0;
        foreach ($feedData as $date => $provinces) {
            if (in_array($date, $missingDates) && isset($provinces[$provinceName])) {
                if ($this->storeRssResult($province, $date, $provinces[$provinceName], $drawTime)) {
                    $stored++;
                }
            }
        }

        Log::info('Filled gaps from RSS', [
            'province' => $province->code,
            'stored' => $stored,
        ]);

        return $stored;
    }

    /**
     * Fetch missing XSMT results from RSS feed
     *
     * @param Province $province Must be a central province
     * @param array $missingDates Array of missing date strings (Y-m-d format)
     * @return int Number of results stored
     */
    public function fetchMissingXSMTFromRss(Province $province, array $missingDates): int
    {
        $feedData = $this->fetchXSMTRssFeed();
        return $this->fetchMissingFromRss($province, $missingDates, $feedData, self::XSMT_PROVINCE_MAP, '17:15:00');
    }

    /**
     * Fetch missing XSMN results from RSS feed
     *
     * @param Province $province Must be a south province
     * @param array $missingDates Array of missing date strings (Y-m-d format)
     * @return int Number of results stored
     */
    public function fetchMissingXSMNFromRss(Province $province, array $missingDates): int
    {
        $feedData = $this->fetchXSMNRssFeed();
        return $this->fetchMissingFromRss($province, $missingDates, $feedData, self::XSMN_PROVINCE_MAP, '16:15:00');
    }

    // =========================================================================
    // XSMB: GitHub CSV
    // =========================================================================

    /**
     * Fetch and store XSMB results from GitHub CSV
     *
     * @param Province $province Must be XSMB Hà Nội (code: miba)
     * @param int $limitNum Number of latest results to store
     * @return int Number of results stored
     */
    public function fetchAndStoreXSMBResults(Province $province, int $limitNum = 1): int
    {
        if ($province->code !== 'miba') {
            Log::warning('fetchAndStoreXSMBResults called with non-XSMB province', [
                'province_code' => $province->code,
            ]);
            return 0;
        }

        $startTime = microtime(true);

        try {
            $response = Http::timeout($this->timeout)->get($this->xsmbGithubUrl);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            if (!$response->successful()) {
                $this->logApiRequest(
                    $this->xsmbGithubUrl,
                    'miba',
                    $response->status(),
                    $responseTime,
                    'GitHub CSV fetch failed',
                    0
                );
                return 0;
            }

            $lines = explode("\n", $response->body());
            array_shift($lines); // Skip header

            // Filter to non-empty lines and take the latest $limitNum rows
            $dataLines = array_filter($lines, fn($line) => !empty(trim($line)));
            $latestLines = array_slice($dataLines, -$limitNum);

            $stored = 0;
            foreach ($latestLines as $line) {
                $fields = str_getcsv($line);
                if (count($fields) < 27) {
                    continue;
                }

                $date = $fields[0];
                $data = [
                    'prize_special' => $fields[1],
                    'prize_1' => $fields[2],
                    'prize_2' => $fields[3] . ',' . $fields[4],
                    'prize_3' => implode(',', array_slice($fields, 5, 6)),
                    'prize_4' => implode(',', array_slice($fields, 11, 4)),
                    'prize_5' => implode(',', array_slice($fields, 15, 6)),
                    'prize_6' => implode(',', array_slice($fields, 21, 3)),
                    'prize_7' => implode(',', array_slice($fields, 24, 4)),
                    'prize_8' => null,
                ];

                if ($this->storeXSMBFromGitHub($province, $date, $data)) {
                    $stored++;
                }
            }

            $this->logApiRequest(
                $this->xsmbGithubUrl,
                'miba',
                $response->status(),
                $responseTime,
                null,
                $stored
            );

            Log::info('XSMB results fetched from GitHub', [
                'stored' => $stored,
                'limit' => $limitNum,
            ]);

            return $stored;

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiRequest(
                $this->xsmbGithubUrl,
                'miba',
                0,
                $responseTime,
                $e->getMessage(),
                0
            );

            Log::error('Failed to fetch XSMB from GitHub', [
                'error' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Fetch XSMB results from GitHub CSV as fallback
     *
     * @param array $targetDates Array of date strings (Y-m-d format) to fetch
     * @return array Array of results indexed by date
     */
    public function fetchXSMBFromGitHub(array $targetDates): array
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->xsmbGithubUrl);

            if (!$response->successful()) {
                Log::warning('Failed to fetch XSMB data from GitHub', [
                    'status' => $response->status(),
                ]);
                return [];
            }

            $results = [];
            $lines = explode("\n", $response->body());

            // Skip header line
            array_shift($lines);

            foreach ($lines as $line) {
                if (empty(trim($line))) {
                    continue;
                }

                $fields = str_getcsv($line);
                if (count($fields) < 27) {
                    continue;
                }

                $date = $fields[0]; // Y-m-d format

                if (!in_array($date, $targetDates)) {
                    continue;
                }

                // Parse CSV fields into prize structure
                $results[$date] = [
                    'prize_special' => $fields[1],
                    'prize_1' => $fields[2],
                    'prize_2' => $fields[3] . ',' . $fields[4],
                    'prize_3' => implode(',', array_slice($fields, 5, 6)),
                    'prize_4' => implode(',', array_slice($fields, 11, 4)),
                    'prize_5' => implode(',', array_slice($fields, 15, 6)),
                    'prize_6' => implode(',', array_slice($fields, 21, 3)),
                    'prize_7' => implode(',', array_slice($fields, 24, 4)),
                    'prize_8' => null,
                ];
            }

            return $results;

        } catch (\Exception $e) {
            Log::error('Failed to fetch XSMB from GitHub', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Store XSMB result from GitHub data
     *
     * @param Province $province
     * @param string $date Date string (Y-m-d format)
     * @param array $data Prize data
     * @return bool
     */
    protected function storeXSMBFromGitHub(Province $province, string $date, array $data): bool
    {
        try {
            $drawDate = Carbon::parse($date);
            $turnNum = $drawDate->format('d/m/Y');

            // Build detail JSON for consistency
            $detail = [
                $data['prize_special'],
                $data['prize_1'],
                $data['prize_2'],
                $data['prize_3'],
                $data['prize_4'],
                $data['prize_5'],
                $data['prize_6'],
                $data['prize_7'],
                $data['prize_8'],
            ];

            LotteryResult::updateOrCreate(
                [
                    'province_id' => $province->id,
                    'turn_num' => $turnNum,
                ],
                [
                    'draw_date' => $date,
                    'draw_time' => '18:35:00',
                    'draw_timestamp' => $drawDate->setTime(18, 35)->timestamp,
                    'open_num' => null,
                    'prize_special' => $data['prize_special'],
                    'prize_1' => $data['prize_1'],
                    'prize_2' => $data['prize_2'],
                    'prize_3' => $data['prize_3'],
                    'prize_4' => $data['prize_4'],
                    'prize_5' => $data['prize_5'],
                    'prize_6' => $data['prize_6'],
                    'prize_7' => $data['prize_7'],
                    'prize_8' => $data['prize_8'],
                    'detail_json' => $detail,
                    'status' => 0,
                ]
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to store XSMB from GitHub', [
                'date' => $date,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Fetch missing XSMB results from GitHub as fallback
     *
     * @param Province $province Must be XSMB Hà Nội (code: miba)
     * @param array $missingDates Array of missing date strings (Y-m-d format)
     * @return int Number of results stored
     */
    public function fetchMissingXSMBFromGitHub(Province $province, array $missingDates): int
    {
        if ($province->code !== 'miba' || empty($missingDates)) {
            return 0;
        }

        Log::info('Attempting to fetch missing XSMB from GitHub', [
            'missing_dates' => $missingDates,
        ]);

        $githubData = $this->fetchXSMBFromGitHub($missingDates);

        if (empty($githubData)) {
            Log::warning('No XSMB data found in GitHub for missing dates');
            return 0;
        }

        $stored = 0;
        foreach ($githubData as $date => $data) {
            if ($this->storeXSMBFromGitHub($province, $date, $data)) {
                $stored++;
            }
        }

        Log::info('Filled XSMB gaps from GitHub', [
            'stored' => $stored,
        ]);

        return $stored;
    }

    // =========================================================================
    // Common: Expected dates & missing results
    // =========================================================================

    /**
     * Get expected draw dates for a province based on its draw_days schedule
     *
     * @param Province $province
     * @param int $daysBack Number of days to look back
     * @return array Array of date strings (Y-m-d format)
     */
    public function getExpectedDrawDates(Province $province, int $daysBack = 30): array
    {
        $dates = [];
        $today = Carbon::today();

        for ($i = 0; $i < $daysBack; $i++) {
            $date = $today->copy()->subDays($i);
            $dayOfWeek = $date->dayOfWeekIso; // 1=Monday, 7=Sunday

            if (in_array($dayOfWeek, $province->draw_days ?? [])) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        return $dates;
    }

    /**
     * Fetch and store only missing results for a province
     *
     * @param Province $province
     * @param int $daysBack Number of days to look back for missing results
     * @return int Number of results stored
     */
    public function fetchMissingResults(Province $province, int $daysBack = 30): int
    {
        // 1. Get expected draw dates
        $expectedDates = $this->getExpectedDrawDates($province, $daysBack);

        if (empty($expectedDates)) {
            Log::info("No expected draw dates for province: {$province->name}");
            return 0;
        }

        // 2. Get existing dates from database
        $existingDates = LotteryResult::where('province_id', $province->id)
            ->whereIn('draw_date', $expectedDates)
            ->pluck('draw_date')
            ->map(fn($d) => $d instanceof Carbon ? $d->format('Y-m-d') : $d)
            ->toArray();

        // 3. Calculate missing dates
        $missingDates = array_values(array_diff($expectedDates, $existingDates));

        if (empty($missingDates)) {
            Log::info("No missing dates for province: {$province->name}");
            return 0;
        }

        Log::info("Found missing dates for province: {$province->name}", [
            'missing_count' => count($missingDates),
            'missing_dates' => $missingDates,
        ]);

        // 4. For XSMB (miba): use GitHub CSV directly
        if ($province->code === 'miba') {
            return $this->fetchMissingXSMBFromGitHub($province, $missingDates);
        }

        // 5. For central provinces: use XSMT RSS feed
        if ($province->region === 'central') {
            return $this->fetchMissingXSMTFromRss($province, $missingDates);
        }

        // 6. For south provinces: use XSMN RSS feed
        return $this->fetchMissingXSMNFromRss($province, $missingDates);
    }
}
