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
    protected string $baseUrl = 'https://xoso188.net/api/front/open/lottery/history/list/game';
    protected int $timeout = 30; // seconds

    /**
     * Fetch lottery results for a specific province
     *
     * @param string $gameCode Province code (e.g., 'miba', 'qung', 'tphc')
     * @param int $limitNum Number of results to fetch
     * @return array|null Returns parsed results or null on failure
     */
    public function fetchResults(string $gameCode, int $limitNum = 30): ?array
    {
        $startTime = microtime(true);
        $endpoint = $this->baseUrl;

        try {
            // Make API request
            $response = Http::timeout($this->timeout)
                ->get($endpoint, [
                    'gameCode' => $gameCode,
                    'limitNum' => $limitNum,
                ]);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log API request
            $this->logApiRequest(
                $endpoint . '?gameCode=' . $gameCode . '&limitNum=' . $limitNum,
                $gameCode,
                $response->status(),
                $responseTime,
                null,
                $response->successful() ? count($response->json('t.issueList', [])) : 0
            );

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log failed request
            $this->logApiRequest(
                $endpoint . '?gameCode=' . $gameCode . '&limitNum=' . $limitNum,
                $gameCode,
                0,
                $responseTime,
                $e->getMessage(),
                0
            );

            Log::error('Lottery API fetch failed', [
                'gameCode' => $gameCode,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch and store lottery results for a province
     *
     * @param Province $province
     * @param int $limitNum
     * @return int Number of results stored
     */
    public function fetchAndStoreResults(Province $province, int $limitNum = 30): int
    {
        $data = $this->fetchResults($province->code, $limitNum);

        if (!$data || !isset($data['t']['issueList'])) {
            return 0;
        }

        $stored = 0;
        foreach ($data['t']['issueList'] as $issue) {
            if ($this->storeResult($province, $issue)) {
                $stored++;
            }
        }

        return $stored;
    }

    /**
     * Store a single lottery result
     *
     * @param Province $province
     * @param array $issue
     * @return bool
     */
    protected function storeResult(Province $province, array $issue): bool
    {
        try {
            // Parse the detail JSON array
            $detail = json_decode($issue['detail'], true);

            if (!$detail || count($detail) < 1) {
                Log::warning('Invalid detail structure', [
                    'turn_num' => $issue['turnNum'] ?? 'unknown',
                    'detail' => $issue['detail'] ?? null,
                ]);
                return false;
            }

            // Parse draw date from turnNum (e.g., "17/01/2026")
            $drawDate = Carbon::createFromFormat('d/m/Y', $issue['turnNum'])->format('Y-m-d');

            // Create or update lottery result
            LotteryResult::updateOrCreate(
                [
                    'province_id' => $province->id,
                    'turn_num' => $issue['turnNum'],
                ],
                [
                    'draw_date' => $drawDate,
                    'draw_time' => $issue['openTime'],
                    'draw_timestamp' => $issue['openTimeStamp'] ?? null,
                    'open_num' => $issue['openNum'] ?? null,
                    'prize_special' => $detail[0] ?? null, // ĐB - 6 digits
                    'prize_1' => $detail[1] ?? null,        // G1 - 5 digits
                    'prize_2' => $detail[2] ?? null,        // G2 - 5 digits
                    'prize_3' => $detail[3] ?? null,        // G3 - comma-separated
                    'prize_4' => $detail[4] ?? null,        // G4 - comma-separated
                    'prize_5' => $detail[5] ?? null,        // G5 - 4 digits
                    'prize_6' => $detail[6] ?? null,        // G6 - comma-separated
                    'prize_7' => $detail[7] ?? null,        // G7 - 3 digits
                    'prize_8' => $detail[8] ?? null,        // G8 - 2 digits
                    'detail_json' => $detail,
                    'status' => $issue['status'] ?? 0,
                ]
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to store lottery result', [
                'province_id' => $province->id,
                'turn_num' => $issue['turnNum'] ?? 'unknown',
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get latest results for a province (cached)
     *
     * @param string $gameCode
     * @param int $limitNum
     * @param int $cacheTTL Cache duration in minutes
     * @return array|null
     */
    public function getCachedResults(string $gameCode, int $limitNum = 10, int $cacheTTL = 15): ?array
    {
        $cacheKey = "lottery_results_{$gameCode}_{$limitNum}";

        return Cache::remember($cacheKey, $cacheTTL * 60, function () use ($gameCode, $limitNum) {
            return $this->fetchResults($gameCode, $limitNum);
        });
    }

    /**
     * Log API request to database
     *
     * @param string $endpoint
     * @param string|null $provinceCode
     * @param int|null $responseStatus
     * @param int|null $responseTime
     * @param string|null $errorMessage
     * @param int $fetchedCount
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

    /**
     * Fetch all active provinces
     *
     * @param int $limitNum
     * @return array Statistics about the fetch operation
     */
    public function fetchAllProvinces(int $limitNum = 30): array
    {
        $provinces = Province::where('is_active', true)->get();
        $stats = [
            'total' => $provinces->count(),
            'successful' => 0,
            'failed' => 0,
            'results_stored' => 0,
        ];

        foreach ($provinces as $province) {
            $stored = $this->fetchAndStoreResults($province, $limitNum);

            if ($stored > 0) {
                $stats['successful']++;
                $stats['results_stored'] += $stored;
            } else {
                $stats['failed']++;
            }
        }

        return $stats;
    }

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
        $missingDates = array_diff($expectedDates, $existingDates);

        if (empty($missingDates)) {
            Log::info("No missing dates for province: {$province->name}");
            return 0;
        }

        Log::info("Found missing dates for province: {$province->name}", [
            'missing_count' => count($missingDates),
            'missing_dates' => array_values($missingDates),
        ]);

        // 4. Fetch enough results to cover the gap
        $data = $this->fetchResults($province->code, $daysBack);

        if (!$data || !isset($data['t']['issueList'])) {
            Log::warning("No data from API for province: {$province->name}");
            return 0;
        }

        // 5. Store only results for missing dates
        $stored = 0;
        foreach ($data['t']['issueList'] as $issue) {
            $drawDate = Carbon::createFromFormat('d/m/Y', $issue['turnNum'])->format('Y-m-d');

            if (in_array($drawDate, $missingDates)) {
                if ($this->storeResult($province, $issue)) {
                    $stored++;
                }
            }
        }

        return $stored;
    }

    /**
     * Fetch today's XSMB Hà Nội results from the new real-time API
     *
     * @return array|null Returns parsed results or null on failure
     */
    public function fetchXSMBTodayFromNewApi(): ?array
    {
        $startTime = microtime(true);
        $endpoint = 'https://api-xsmb-today.onrender.com/api/v1';

        try {
            // Make API request
            $response = Http::timeout($this->timeout)->get($endpoint);

            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log API request
            $this->logApiRequest(
                $endpoint,
                'miba', // XSMB Hà Nội code
                $response->status(),
                $responseTime,
                null,
                $response->successful() ? 1 : 0
            );

            if ($response->successful()) {
                return $response->json();
            }

            return null;

        } catch (\Exception $e) {
            $responseTime = (int) ((microtime(true) - $startTime) * 1000);

            // Log failed request
            $this->logApiRequest(
                $endpoint,
                'miba',
                0,
                $responseTime,
                $e->getMessage(),
                0
            );

            Log::error('XSMB Today API fetch failed', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Fetch and store today's XSMB Hà Nội results from the new API
     *
     * @param Province $province Must be Hà Nội (code: miba)
     * @return bool Success status
     */
    public function fetchAndStoreXSMBToday(Province $province): bool
    {
        // Only works for XSMB Hà Nội
        if ($province->code !== 'miba') {
            Log::warning('fetchAndStoreXSMBToday called with non-XSMB province', [
                'province_code' => $province->code,
            ]);
            return false;
        }

        $data = $this->fetchXSMBTodayFromNewApi();

        if (!$data || !isset($data['results'])) {
            Log::warning('Invalid response from XSMB Today API, falling back to old API');
            // Fallback to old API
            return $this->fetchAndStoreResults($province, 1) > 0;
        }

        return $this->storeXSMBTodayResult($province, $data);
    }

    /**
     * Store XSMB result from the new API format
     *
     * @param Province $province
     * @param array $data Data from new API
     * @return bool
     */
    protected function storeXSMBTodayResult(Province $province, array $data): bool
    {
        try {
            $results = $data['results'];
            $dateString = $data['time']; // Format: "19-1-2026"

            // Parse date from "d-m-Y" format
            $drawDate = Carbon::createFromFormat('d-m-Y', $dateString);
            $turnNum = $drawDate->format('d/m/Y'); // Convert to "d/m/Y" for consistency

            // Map new API format to database fields
            $prizeSpecial = $results['ĐB'][0] ?? null;
            $prize1 = $results['G1'][0] ?? null;
            $prize2 = implode(',', $results['G2'] ?? []);
            $prize3 = implode(',', $results['G3'] ?? []);
            $prize4 = implode(',', $results['G4'] ?? []);
            $prize5 = implode(',', $results['G5'] ?? []);
            $prize6 = implode(',', $results['G6'] ?? []);
            $prize7 = implode(',', $results['G7'] ?? []);
            $prize8 = null; // New API doesn't have G8

            // Build detail JSON in old format for consistency
            $detail = [
                $prizeSpecial,
                $prize1,
                $prize2,
                $prize3,
                $prize4,
                $prize5,
                $prize6,
                $prize7,
                $prize8,
            ];

            // Estimate draw time (XSMB typically ends around 18:35)
            $drawTime = '18:35:00';

            // Create or update lottery result
            LotteryResult::updateOrCreate(
                [
                    'province_id' => $province->id,
                    'turn_num' => $turnNum,
                ],
                [
                    'draw_date' => $drawDate->format('Y-m-d'),
                    'draw_time' => $drawTime,
                    'draw_timestamp' => $drawDate->setTime(18, 35)->timestamp,
                    'open_num' => null,
                    'prize_special' => $prizeSpecial,
                    'prize_1' => $prize1,
                    'prize_2' => $prize2,
                    'prize_3' => $prize3,
                    'prize_4' => $prize4,
                    'prize_5' => $prize5,
                    'prize_6' => $prize6,
                    'prize_7' => $prize7,
                    'prize_8' => $prize8,
                    'detail_json' => $detail,
                    'status' => 0,
                ]
            );

            Log::info('XSMB Today result stored successfully', [
                'province' => $province->name,
                'date' => $turnNum,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to store XSMB Today result', [
                'province_id' => $province->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
