<?php

namespace App\Services;

use App\Models\LotteryResult;
use App\Models\NumberStatistic;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatisticsService
{
    /**
     * Generate statistics for a province
     *
     * @param Province $province
     * @return int Number of statistics generated
     */
    public function generateStatisticsForProvince(Province $province): int
    {
        Log::info("Generating statistics for province: {$province->name}");

        $generated = 0;

        // Generate statistics for each 2-digit number (00-99)
        for ($num = 0; $num <= 99; $num++) {
            $number = str_pad($num, 2, '0', STR_PAD_LEFT);

            $statistics = $this->calculateNumberStatistics($province, $number);

            NumberStatistic::updateOrCreate(
                [
                    'province_id' => $province->id,
                    'number' => $number,
                ],
                $statistics
            );

            $generated++;
        }

        Log::info("Generated {$generated} statistics for province: {$province->name}");

        return $generated;
    }

    /**
     * Calculate statistics for a specific number
     *
     * @param Province $province
     * @param string $number
     * @return array
     */
    protected function calculateNumberStatistics(Province $province, string $number): array
    {
        $periods = [30, 60, 90, 100, 200, 300, 500];
        $statistics = [];

        foreach ($periods as $days) {
            $startDate = Carbon::now()->subDays($days);
            $frequency = $this->countNumberOccurrences($province->id, $number, $startDate);
            $statistics["frequency_{$days}d"] = $frequency;
        }

        // Get last appeared date and cycle count
        $lastAppeared = $this->getLastAppearedDate($province->id, $number);
        $statistics['last_appeared'] = $lastAppeared;
        $statistics['cycle_count'] = $lastAppeared
            ? Carbon::parse($lastAppeared)->diffInDays(Carbon::now())
            : 0;
        $statistics['updated_at'] = now();

        return $statistics;
    }

    /**
     * Count occurrences of a number in lottery results
     *
     * @param int $provinceId
     * @param string $number
     * @param Carbon $startDate
     * @return int
     */
    protected function countNumberOccurrences(int $provinceId, string $number, Carbon $startDate): int
    {
        $count = 0;

        // Get all results since start date
        $results = LotteryResult::where('province_id', $provinceId)
            ->where('draw_date', '>=', $startDate)
            ->get();

        foreach ($results as $result) {
            // Check all prize fields for the number (last 2 digits)
            $prizes = [
                $result->prize_special,
                $result->prize_1,
                $result->prize_2,
                $result->prize_3,
                $result->prize_4,
                $result->prize_5,
                $result->prize_6,
                $result->prize_7,
                $result->prize_8,
            ];

            foreach ($prizes as $prize) {
                if (!$prize) {
                    continue;
                }

                // Handle comma-separated prizes
                if (str_contains($prize, ',')) {
                    $prizeNumbers = explode(',', $prize);
                    foreach ($prizeNumbers as $prizeNum) {
                        if ($this->matchesNumber($prizeNum, $number)) {
                            $count++;
                        }
                    }
                } else {
                    if ($this->matchesNumber($prize, $number)) {
                        $count++;
                    }
                }
            }
        }

        return $count;
    }

    /**
     * Check if a prize number matches the target number (last 2 digits)
     *
     * @param string $prizeNumber
     * @param string $targetNumber
     * @return bool
     */
    protected function matchesNumber(string $prizeNumber, string $targetNumber): bool
    {
        $prizeNumber = trim($prizeNumber);

        if (strlen($prizeNumber) < 2) {
            return false;
        }

        $lastTwoDigits = substr($prizeNumber, -2);
        return $lastTwoDigits === $targetNumber;
    }

    /**
     * Get the last date when a number appeared
     *
     * @param int $provinceId
     * @param string $number
     * @return string|null
     */
    protected function getLastAppearedDate(int $provinceId, string $number): ?string
    {
        $results = LotteryResult::where('province_id', $provinceId)
            ->orderBy('draw_date', 'desc')
            ->get();

        foreach ($results as $result) {
            $prizes = [
                $result->prize_special,
                $result->prize_1,
                $result->prize_2,
                $result->prize_3,
                $result->prize_4,
                $result->prize_5,
                $result->prize_6,
                $result->prize_7,
                $result->prize_8,
            ];

            foreach ($prizes as $prize) {
                if (!$prize) {
                    continue;
                }

                if (str_contains($prize, ',')) {
                    $prizeNumbers = explode(',', $prize);
                    foreach ($prizeNumbers as $prizeNum) {
                        if ($this->matchesNumber($prizeNum, $number)) {
                            return $result->draw_date->format('Y-m-d');
                        }
                    }
                } else {
                    if ($this->matchesNumber($prize, $number)) {
                        return $result->draw_date->format('Y-m-d');
                    }
                }
            }
        }

        return null;
    }

    /**
     * Generate statistics for all active provinces
     *
     * @return array Statistics about the generation
     */
    public function generateStatisticsForAllProvinces(): array
    {
        $provinces = Province::where('is_active', true)->get();
        $stats = [
            'total_provinces' => $provinces->count(),
            'total_statistics' => 0,
        ];

        foreach ($provinces as $province) {
            $generated = $this->generateStatisticsForProvince($province);
            $stats['total_statistics'] += $generated;
        }

        return $stats;
    }
}
