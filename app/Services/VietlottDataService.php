<?php

namespace App\Services;

use App\Models\VietlottResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * VietlottDataService - Fetches Vietlott lottery results from GitHub
 *
 * Uses data from vietvudanh/vietlott-data repository which provides
 * historical and current lottery results in JSONL format.
 */
class VietlottDataService
{
    private const DATA_URLS = [
        'mega645' => 'https://raw.githubusercontent.com/vietvudanh/vietlott-data/main/data/power645.jsonl',
        'power655' => 'https://raw.githubusercontent.com/vietvudanh/vietlott-data/main/data/power655.jsonl',
        'max3d' => 'https://raw.githubusercontent.com/vietvudanh/vietlott-data/main/data/3d.jsonl',
        'max3dpro' => 'https://raw.githubusercontent.com/vietvudanh/vietlott-data/main/data/3d_pro.jsonl',
    ];

    private const GAME_INFO = [
        'mega645' => [
            'name' => 'Mega 6/45',
            'description' => 'Chọn 6 số từ 01-45',
            'schedule' => 'Thứ 4, Thứ 6, Chủ nhật',
            'draw_time' => '18:00',
            'ticket_price' => '10,000',
        ],
        'power655' => [
            'name' => 'Power 6/55',
            'description' => 'Chọn 6 số từ 01-55',
            'schedule' => 'Thứ 3, Thứ 5, Thứ 7',
            'draw_time' => '18:00',
            'ticket_price' => '10,000',
        ],
        'max3d' => [
            'name' => 'Max 3D',
            'description' => 'Chọn 3 số từ 000-999',
            'schedule' => 'Hàng ngày',
            'draw_time' => '18:00',
            'ticket_price' => '10,000',
        ],
        'max3dpro' => [
            'name' => 'Max 3D Pro',
            'description' => 'Chọn 3 số từ 000-999 (Nâng cao)',
            'schedule' => 'Thứ 2, Thứ 4, Thứ 6',
            'draw_time' => '18:00',
            'ticket_price' => '10,000',
        ],
    ];

    /**
     * Get available game types
     */
    public static function getGameTypes(): array
    {
        return array_keys(self::DATA_URLS);
    }

    /**
     * Get game info by type
     */
    public static function getGameInfo(string $gameType): ?array
    {
        return self::GAME_INFO[$gameType] ?? null;
    }

    /**
     * Get all game info
     */
    public static function getAllGameInfo(): array
    {
        return self::GAME_INFO;
    }

    /**
     * Sync results for a specific game from GitHub
     *
     * @param string $gameType Game type to sync
     * @param bool $force If true, re-import all records regardless of existing data
     * @return array Stats: ['new' => int, 'skipped' => int, 'errors' => int]
     */
    public function syncGame(string $gameType, bool $force = false): array
    {
        if (!isset(self::DATA_URLS[$gameType])) {
            throw new \InvalidArgumentException("Unknown game type: {$gameType}");
        }

        $stats = ['new' => 0, 'skipped' => 0, 'errors' => 0];

        // Get latest draw_number from DB (skip if force)
        $latestId = $force ? null : VietlottResult::where('game_type', $gameType)
            ->max('draw_number');

        // Fetch JSONL from GitHub
        $url = self::DATA_URLS[$gameType];

        try {
            $response = Http::timeout(60)->get($url);

            if (!$response->successful()) {
                Log::error("Failed to fetch Vietlott data from GitHub", [
                    'game' => $gameType,
                    'url' => $url,
                    'status' => $response->status(),
                ]);
                throw new \RuntimeException("HTTP {$response->status()} fetching {$gameType} data");
            }

            $lines = array_filter(explode("\n", $response->body()));

            // Process from end (most recent first) for early termination
            foreach (array_reverse($lines) as $line) {
                $data = json_decode($line, true);

                if (!$data || !isset($data['date'], $data['id'], $data['result'])) {
                    continue;
                }

                $drawNumber = (int) $data['id'];

                // Skip if already stored (early termination when not forcing)
                if (!$force && $latestId !== null && $drawNumber <= $latestId) {
                    $stats['skipped']++;
                    continue;
                }

                // Store new result
                try {
                    VietlottResult::updateOrCreate(
                        [
                            'game_type' => $gameType,
                            'draw_number' => $drawNumber,
                        ],
                        [
                            'draw_date' => Carbon::parse($data['date']),
                            'winning_numbers' => $data['result'],
                            'jackpot_amount' => 0,
                            'prize_breakdown' => null,
                        ]
                    );
                    $stats['new']++;
                } catch (\Exception $e) {
                    Log::warning("Failed to store Vietlott result", [
                        'game' => $gameType,
                        'id' => $data['id'],
                        'error' => $e->getMessage(),
                    ]);
                    $stats['errors']++;
                }
            }

        } catch (\Exception $e) {
            Log::error("Failed to sync Vietlott game", [
                'game' => $gameType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        return $stats;
    }

    /**
     * Sync results for all games
     *
     * @param bool $force If true, re-import all records
     * @return array Stats per game type
     */
    public function syncAllGames(bool $force = false): array
    {
        $results = [];

        foreach (array_keys(self::DATA_URLS) as $gameType) {
            try {
                $results[$gameType] = $this->syncGame($gameType, $force);
            } catch (\Exception $e) {
                $results[$gameType] = [
                    'new' => 0,
                    'skipped' => 0,
                    'errors' => 0,
                    'error_message' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}
