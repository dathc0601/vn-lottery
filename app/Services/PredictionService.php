<?php

namespace App\Services;

use App\Models\LotteryResult;
use App\Models\NumberStatistic;
use App\Models\Prediction;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PredictionService
{
    public const REGION_PROVINCE_MAP = [
        Prediction::REGION_NORTH => 'ha-noi',
        Prediction::REGION_CENTRAL => null, // Uses multiple provinces
        Prediction::REGION_SOUTH => null, // Uses multiple provinces
    ];

    /**
     * Generate prediction for a specific region and date.
     */
    public function generatePrediction(string $region, Carbon $predictionDate): ?Prediction
    {
        $referenceDate = $predictionDate->copy()->subDay();

        // Get previous day's lottery results for reference
        $lotteryResults = $this->getLotteryResultsForRegion($region, $referenceDate);

        if ($lotteryResults->isEmpty()) {
            Log::warning("No lottery results found for region {$region} on {$referenceDate->format('Y-m-d')}");
            return null;
        }

        // Get statistics for the region
        $statistics = $this->getStatisticsForRegion($region);

        // Generate prediction data
        $predictionsData = $this->generatePredictionsData($region, $lotteryResults, $statistics);

        // Generate analysis data (beautiful numbers)
        $analysisData = $this->generateAnalysisData($region, $lotteryResults, $statistics);

        // Create statistics snapshot
        $statisticsSnapshot = $this->createStatisticsSnapshot($statistics);

        // Create lottery results snapshot
        $lotteryResultsSnapshot = $this->createLotteryResultsSnapshot($lotteryResults);

        // Create or update prediction
        $prediction = Prediction::updateOrCreate(
            [
                'region' => $region,
                'prediction_date' => $predictionDate->format('Y-m-d'),
            ],
            [
                'reference_date' => $referenceDate->format('Y-m-d'),
                'predictions_data' => $predictionsData,
                'analysis_data' => $analysisData,
                'statistics_snapshot' => $statisticsSnapshot,
                'lottery_results_snapshot' => $lotteryResultsSnapshot,
                'status' => 'generated',
                'generated_at' => now(),
            ]
        );

        Log::info("Generated prediction for region {$region} on {$predictionDate->format('Y-m-d')}");

        return $prediction;
    }

    /**
     * Get lottery results for a specific region on a given date.
     */
    protected function getLotteryResultsForRegion(string $region, Carbon $date): Collection
    {
        $query = LotteryResult::with('province')
            ->whereDate('draw_date', $date)
            ->whereHas('province', function ($q) use ($region) {
                $q->where('region', $region);
            });

        return $query->get();
    }

    /**
     * Get statistics for a region.
     */
    protected function getStatisticsForRegion(string $region): Collection
    {
        return NumberStatistic::whereHas('province', function ($q) use ($region) {
            $q->where('region', $region);
        })->get();
    }

    /**
     * Generate main prediction numbers.
     */
    protected function generatePredictionsData(string $region, Collection $lotteryResults, Collection $statistics): array
    {
        $specialPrizes = $lotteryResults->pluck('prize_special')->filter()->toArray();

        $data = [
            'head_tail' => $this->calculateHeadTail($specialPrizes, $statistics),
            'loto_2_digit' => $this->calculateLoto2Digit($statistics),
            'loto_3_digit' => $this->calculateLoto3Digit($specialPrizes, $lotteryResults),
            'vip_4_digit' => $this->calculateVip4Digit($specialPrizes, $statistics),
        ];

        // For XSMN/XSMT regions, add per-province and region aggregate data
        if ($region === Prediction::REGION_SOUTH || $region === Prediction::REGION_CENTRAL) {
            $data['region_aggregate'] = $this->generateRegionAggregatePredictions($lotteryResults, $statistics);
            $data['per_province'] = $this->generatePerProvincePredictions($lotteryResults, $statistics);
        }

        return $data;
    }

    /**
     * Generate analysis data (beautiful numbers).
     */
    protected function generateAnalysisData(string $region, Collection $lotteryResults, Collection $statistics): array
    {
        $specialPrizes = $lotteryResults->pluck('prize_special')->filter()->toArray();

        $data = [
            'bach_thu' => $this->calculateBachThu($statistics),
            'lat_lien_tuc' => $this->calculateLatLienTuc($region),
            'cau_2_nhay' => $this->calculateCau2Nhay($region),
            'pascal_triangle' => $this->calculatePascalTriangle($region),
            'lo_kep' => $this->calculateLoKep($lotteryResults),
            'loto_hay_ve' => $this->calculateLotoHayVe($statistics),
        ];

        // For XSMN/XSMT regions, add per-province and region aggregate analysis
        if ($region === Prediction::REGION_SOUTH || $region === Prediction::REGION_CENTRAL) {
            $data['region_aggregate'] = $this->generateRegionAggregateAnalysis($lotteryResults, $statistics);
            $data['per_province'] = $this->generatePerProvinceAnalysis($lotteryResults, $statistics);
        }

        return $data;
    }

    /**
     * Calculate head and tail from special prize.
     * Đầu đuôi giải ĐB
     */
    protected function calculateHeadTail(array $specialPrizes, Collection $statistics): array
    {
        $heads = [];
        $tails = [];

        foreach ($specialPrizes as $prize) {
            if (strlen($prize) >= 2) {
                $lastTwo = substr($prize, -2);
                $heads[] = $lastTwo[0];
                $tails[] = $lastTwo[1];
            }
        }

        // Add frequency-weighted suggestions
        $topStats = $statistics->sortByDesc('frequency_30d')->take(20);
        foreach ($topStats as $stat) {
            $heads[] = $stat->number[0];
            $tails[] = $stat->number[1];
        }

        // Get unique and most frequent
        $headCounts = array_count_values($heads);
        $tailCounts = array_count_values($tails);

        arsort($headCounts);
        arsort($tailCounts);

        return [
            'head' => array_slice(array_keys($headCounts), 0, 3),
            'tail' => array_slice(array_keys($tailCounts), 0, 3),
            'combined' => $this->combineHeadTail(array_keys($headCounts), array_keys($tailCounts)),
        ];
    }

    /**
     * Combine head and tail digits to form 2-digit numbers.
     */
    protected function combineHeadTail(array $heads, array $tails): array
    {
        $combined = [];
        $count = 0;
        foreach (array_slice($heads, 0, 3) as $head) {
            foreach (array_slice($tails, 0, 3) as $tail) {
                $combined[] = $head . $tail;
                $count++;
                if ($count >= 5) {
                    break 2;
                }
            }
        }
        return $combined;
    }

    /**
     * Calculate loto 2-digit predictions based on frequency.
     * Loto 2 số hay về
     */
    protected function calculateLoto2Digit(Collection $statistics): array
    {
        // Get top frequency numbers from last 30 days
        $topFrequency = $statistics
            ->groupBy('number')
            ->map(function ($group) {
                return $group->sum('frequency_30d');
            })
            ->sortDesc()
            ->take(10)
            ->keys()
            ->toArray();

        // Get numbers with high cycle count (due to appear)
        $highCycle = $statistics
            ->groupBy('number')
            ->map(function ($group) {
                return $group->max('cycle_count');
            })
            ->sortDesc()
            ->take(5)
            ->keys()
            ->toArray();

        // Merge and deduplicate
        $merged = array_unique(array_merge($topFrequency, $highCycle));

        return array_slice($merged, 0, 10);
    }

    /**
     * Calculate loto 3-digit predictions.
     * Lô tô 3 số - 3 càng đẹp
     */
    protected function calculateLoto3Digit(array $specialPrizes, Collection $lotteryResults): array
    {
        $numbers = [];

        // Extract last 3 digits from special prizes
        foreach ($specialPrizes as $prize) {
            if (strlen($prize) >= 3) {
                $numbers[] = substr($prize, -3);
            }
        }

        // Add patterns from prize_1 and prize_2
        foreach ($lotteryResults as $result) {
            foreach (['prize_1', 'prize_2'] as $field) {
                $prize = $result->$field;
                if ($prize && strlen($prize) >= 3) {
                    $prizes = explode(',', $prize);
                    foreach ($prizes as $p) {
                        $p = trim($p);
                        if (strlen($p) >= 3) {
                            $numbers[] = substr($p, -3);
                        }
                    }
                }
            }
        }

        return array_slice(array_unique($numbers), 0, 5);
    }

    /**
     * Calculate VIP 4-digit predictions.
     * Soi cầu 4 số VIP
     */
    protected function calculateVip4Digit(array $specialPrizes, Collection $statistics): array
    {
        $numbers = [];

        // Extract last 4 digits from special prizes
        foreach ($specialPrizes as $prize) {
            if (strlen($prize) >= 4) {
                $numbers[] = substr($prize, -4);
            }
        }

        // Combine head_tail results with top frequency
        $topFrequency = $statistics
            ->groupBy('number')
            ->map(function ($group) {
                return $group->sum('frequency_30d');
            })
            ->sortDesc()
            ->take(5)
            ->keys()
            ->toArray();

        // Create 4-digit combinations
        foreach ($topFrequency as $num) {
            if (count($numbers) < 5) {
                $numbers[] = str_pad($num, 4, '0', STR_PAD_LEFT);
            }
        }

        return array_slice(array_unique($numbers), 0, 5);
    }

    /**
     * Calculate Bạch thủ (highest gap numbers - due to appear).
     */
    protected function calculateBachThu(Collection $statistics): array
    {
        return $statistics
            ->groupBy('number')
            ->map(function ($group) {
                return $group->max('cycle_count');
            })
            ->sortDesc()
            ->take(3)
            ->keys()
            ->toArray();
    }

    /**
     * Calculate Lật liên tục (numbers flipping frequently).
     */
    protected function calculateLatLienTuc(string $region): array
    {
        // Get last 10 draws for the region
        $results = LotteryResult::whereHas('province', function ($q) use ($region) {
            $q->where('region', $region);
        })
            ->orderBy('draw_date', 'desc')
            ->take(10)
            ->get();

        $appearances = [];
        foreach ($results as $result) {
            $allPrizes = $this->extractAllLotoNumbers($result);
            foreach ($allPrizes as $num) {
                $appearances[$num] = ($appearances[$num] ?? 0) + 1;
            }
        }

        // Find numbers appearing in ~50% of draws (flipping)
        $flipping = [];
        foreach ($appearances as $num => $count) {
            if ($count >= 4 && $count <= 6) {
                $flipping[$num] = $count;
            }
        }

        arsort($flipping);
        return array_slice(array_keys($flipping), 0, 5);
    }

    /**
     * Calculate Cầu 2 nháy (numbers appearing 2 consecutive draws).
     */
    protected function calculateCau2Nhay(string $region): array
    {
        // Get last 3 draws
        $results = LotteryResult::whereHas('province', function ($q) use ($region) {
            $q->where('region', $region);
        })
            ->orderBy('draw_date', 'desc')
            ->take(3)
            ->get();

        if ($results->count() < 2) {
            return [];
        }

        $lastDraw = $this->extractAllLotoNumbers($results->first());
        $previousDraw = $this->extractAllLotoNumbers($results->skip(1)->first());

        // Find numbers appearing in both draws
        $consecutive = array_intersect($lastDraw, $previousDraw);

        return array_slice(array_values($consecutive), 0, 5);
    }

    /**
     * Calculate Pascal triangle from last 5 special prizes.
     */
    protected function calculatePascalTriangle(string $region): array
    {
        // Get last 5 special prizes
        $results = LotteryResult::whereHas('province', function ($q) use ($region) {
            $q->where('region', $region);
        })
            ->orderBy('draw_date', 'desc')
            ->take(5)
            ->pluck('prize_special')
            ->filter()
            ->map(function ($prize) {
                return (int) substr($prize, -2);
            })
            ->toArray();

        if (count($results) < 3) {
            return [];
        }

        // Apply Pascal triangle arithmetic
        $pascal = [];
        $row = array_values($results);

        while (count($row) > 1) {
            $newRow = [];
            for ($i = 0; $i < count($row) - 1; $i++) {
                $sum = ($row[$i] + $row[$i + 1]) % 10;
                $newRow[] = $sum;
            }
            $row = $newRow;
        }

        // Generate predictions from Pascal result
        if (!empty($row)) {
            $base = $row[0];
            $pascal[] = str_pad($base, 2, '0', STR_PAD_LEFT);
            $pascal[] = str_pad(($base + 5) % 10, 2, '0', STR_PAD_LEFT);
            $pascal[] = str_pad($base . (($base + 1) % 10), 2, '0', STR_PAD_LEFT);
        }

        return array_slice($pascal, 0, 3);
    }

    /**
     * Calculate Lô kẹp (bracketed loto patterns).
     */
    protected function calculateLoKep(Collection $lotteryResults): array
    {
        $patterns = [];

        foreach ($lotteryResults as $result) {
            $allNumbers = $this->extractAllLotoNumbers($result);
            foreach ($allNumbers as $num) {
                if (strlen($num) == 2 && $num[0] == $num[1]) {
                    // Double digit like 11, 22, 33
                    $patterns[] = $num;
                }
            }
        }

        // Also add common kep patterns
        $keps = ['00', '11', '22', '33', '44', '55', '66', '77', '88', '99'];
        $commonKeps = array_intersect($keps, $patterns);

        // If not enough, add most common keps
        if (count($commonKeps) < 3) {
            $commonKeps = array_merge($commonKeps, array_slice($keps, 0, 3 - count($commonKeps)));
        }

        return array_slice(array_unique($commonKeps), 0, 5);
    }

    /**
     * Calculate Lô tô hay về (most frequent in last 30 draws).
     */
    protected function calculateLotoHayVe(Collection $statistics): array
    {
        return $statistics
            ->groupBy('number')
            ->map(function ($group) {
                return $group->sum('frequency_30d');
            })
            ->sortDesc()
            ->take(10)
            ->keys()
            ->toArray();
    }

    /**
     * Extract all 2-digit loto numbers from a lottery result.
     */
    protected function extractAllLotoNumbers(LotteryResult $result): array
    {
        $numbers = [];
        $prizeFields = [
            'prize_special', 'prize_1', 'prize_2', 'prize_3',
            'prize_4', 'prize_5', 'prize_6', 'prize_7', 'prize_8',
        ];

        foreach ($prizeFields as $field) {
            $prize = $result->$field;
            if (!$prize) {
                continue;
            }

            $prizes = explode(',', $prize);
            foreach ($prizes as $p) {
                $p = trim($p);
                if (strlen($p) >= 2) {
                    $numbers[] = substr($p, -2);
                }
            }
        }

        return array_unique($numbers);
    }

    /**
     * Create statistics snapshot for storage.
     */
    protected function createStatisticsSnapshot(Collection $statistics): array
    {
        // Group by number and aggregate
        $snapshot = [
            'top_frequency_30d' => $statistics
                ->groupBy('number')
                ->map(fn($group) => $group->sum('frequency_30d'))
                ->sortDesc()
                ->take(20)
                ->toArray(),
            'top_gap' => $statistics
                ->groupBy('number')
                ->map(fn($group) => $group->max('cycle_count'))
                ->sortDesc()
                ->take(20)
                ->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];

        return $snapshot;
    }

    /**
     * Create lottery results snapshot.
     */
    protected function createLotteryResultsSnapshot(Collection $lotteryResults): array
    {
        return $lotteryResults->map(function ($result) {
            return [
                'province_id' => $result->province_id,
                'province_name' => $result->province?->name ?? 'Unknown',
                'draw_date' => $result->draw_date->format('Y-m-d'),
                'prize_special' => $result->prize_special,
                'prize_1' => $result->prize_1,
                'prize_2' => $result->prize_2,
                'prize_3' => $result->prize_3,
                'prize_4' => $result->prize_4,
                'prize_5' => $result->prize_5,
                'prize_6' => $result->prize_6,
                'prize_7' => $result->prize_7,
                'prize_8' => $result->prize_8,
            ];
        })->values()->toArray();
    }

    /**
     * Publish a prediction.
     */
    public function publishPrediction(Prediction $prediction): bool
    {
        $prediction->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return true;
    }

    /**
     * Get last 10 special prizes for a region.
     */
    public function getLast10SpecialPrizes(string $region): Collection
    {
        return LotteryResult::with('province')
            ->whereHas('province', function ($q) use ($region) {
                $q->where('region', $region);
            })
            ->whereNotNull('prize_special')
            ->orderBy('draw_date', 'desc')
            ->take(10)
            ->get(['id', 'province_id', 'draw_date', 'prize_special']);
    }

    /**
     * Get last 30 special prizes for a region.
     */
    public function getLast30SpecialPrizes(string $region): Collection
    {
        return LotteryResult::with('province')
            ->whereHas('province', function ($q) use ($region) {
                $q->where('region', $region);
            })
            ->whereNotNull('prize_special')
            ->orderBy('draw_date', 'desc')
            ->take(30)
            ->get(['id', 'province_id', 'draw_date', 'prize_special']);
    }

    /**
     * Get provinces that draw on a specific date for a region.
     */
    public function getProvincesForRegionAndDate(string $region, Carbon $date): Collection
    {
        $dayOfWeek = $date->dayOfWeek; // 0 = Sunday, 6 = Saturday

        return Province::where('region', $region)
            ->where('is_active', true)
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                $drawDays = $province->draw_days ?? [];
                return in_array($dayOfWeek, $drawDays);
            })
            ->sortBy('sort_order')
            ->values();
    }

    /**
     * Generate region aggregate predictions for multi-province regions (XSMN/XSMT).
     */
    protected function generateRegionAggregatePredictions(Collection $lotteryResults, Collection $statistics): array
    {
        $loto2Digit = $this->calculateLoto2Digit($statistics);

        return [
            'bao_lo_4_dai' => array_slice($loto2Digit, 0, 4),
            'xien_2' => array_slice($loto2Digit, 0, 2),
            '3_cang_dep' => $this->calculateLoto3Digit(
                $lotteryResults->pluck('prize_special')->filter()->toArray(),
                $lotteryResults
            ),
        ];
    }

    /**
     * Generate per-province predictions for multi-province regions.
     */
    protected function generatePerProvincePredictions(Collection $lotteryResults, Collection $statistics): array
    {
        $perProvince = [];

        foreach ($lotteryResults as $result) {
            $province = $result->province;
            if (!$province) {
                continue;
            }

            $provinceSlug = $province->slug;
            $specialPrize = $result->prize_special;
            $prize8 = $result->prize_8;

            // Get province-specific statistics
            $provinceStats = $statistics->where('province_id', $province->id);

            // Calculate giai tam (last 2 digits of prize_8)
            $giaiTam = $prize8 ? substr(trim(explode(',', $prize8)[0]), -2) : null;

            // Calculate dac biet head and tail
            $dacBietHead = null;
            $dacBietTail = null;
            if ($specialPrize && strlen($specialPrize) >= 2) {
                $lastTwo = substr($specialPrize, -2);
                $dacBietHead = $lastTwo[0];
                $dacBietTail = $lastTwo[1];
            }

            // Calculate bao lo 2 so from province statistics
            $baoLo2 = $provinceStats->isNotEmpty()
                ? $provinceStats->sortByDesc('frequency_30d')->take(3)->pluck('number')->toArray()
                : $this->calculateLoto2DigitFromResult($result);

            $perProvince[$provinceSlug] = [
                'province_id' => $province->id,
                'province_name' => $province->name,
                'giai_tam' => $giaiTam,
                'dac_biet_head' => $dacBietHead,
                'dac_biet_tail' => $dacBietTail,
                'bao_lo_2' => $baoLo2,
            ];
        }

        return $perProvince;
    }

    /**
     * Generate region aggregate analysis for multi-province regions.
     */
    protected function generateRegionAggregateAnalysis(Collection $lotteryResults, Collection $statistics): array
    {
        $loGanSummary = [];
        $loNongSummary = [];

        foreach ($lotteryResults as $result) {
            $province = $result->province;
            if (!$province) {
                continue;
            }

            $provinceStats = $statistics->where('province_id', $province->id);

            if ($provinceStats->isNotEmpty()) {
                // Find lo gan (highest cycle count)
                $topGan = $provinceStats->sortByDesc('cycle_count')->first();
                if ($topGan) {
                    $loGanSummary[] = [
                        'number' => $topGan->number,
                        'province' => $province->name,
                        'days' => $topGan->cycle_count,
                    ];
                }

                // Find lo nong (highest frequency)
                $topNong = $provinceStats->sortByDesc('frequency_30d')->first();
                if ($topNong) {
                    $loNongSummary[] = [
                        'number' => $topNong->number,
                        'province' => $province->name,
                        'times' => $topNong->frequency_30d,
                    ];
                }
            }
        }

        // Sort summaries by days/times descending
        usort($loGanSummary, fn($a, $b) => $b['days'] <=> $a['days']);
        usort($loNongSummary, fn($a, $b) => $b['times'] <=> $a['times']);

        return [
            'lo_gan_summary' => array_slice($loGanSummary, 0, 5),
            'lo_nong_summary' => array_slice($loNongSummary, 0, 5),
        ];
    }

    /**
     * Generate per-province analysis for multi-province regions.
     */
    protected function generatePerProvinceAnalysis(Collection $lotteryResults, Collection $statistics): array
    {
        $perProvince = [];

        foreach ($lotteryResults as $result) {
            $province = $result->province;
            if (!$province) {
                continue;
            }

            $provinceSlug = $province->slug;
            $provinceStats = $statistics->where('province_id', $province->id);

            // Calculate bach thu (highest gap)
            $bachThu = null;
            $loGan = null;
            if ($provinceStats->isNotEmpty()) {
                $topGan = $provinceStats->sortByDesc('cycle_count')->first();
                if ($topGan) {
                    $bachThu = $topGan->number;
                    $loGan = [
                        'number' => $topGan->number,
                        'days' => $topGan->cycle_count,
                    ];
                }
            }

            // Calculate lat lien tuc from result
            $latLienTuc = $this->calculateLatLienTucForProvince($province->id);

            // Calculate pascal from province special prizes
            $pascal = $this->calculatePascalForProvince($province->id);

            // Calculate lo kep
            $loKep = $this->calculateLoKepFromResult($result);

            // Calculate loto hay ve
            $lotoHayVe = $provinceStats->isNotEmpty()
                ? $provinceStats->sortByDesc('frequency_30d')->take(5)->pluck('number')->toArray()
                : [];

            $perProvince[$provinceSlug] = [
                'bach_thu' => $bachThu,
                'lat_lien_tuc' => $latLienTuc,
                'lo_gan' => $loGan,
                'pascal' => $pascal,
                'lo_kep' => $loKep,
                'loto_hay_ve' => $lotoHayVe,
            ];
        }

        return $perProvince;
    }

    /**
     * Calculate loto 2-digit from a single lottery result.
     */
    protected function calculateLoto2DigitFromResult(LotteryResult $result): array
    {
        $numbers = $this->extractAllLotoNumbers($result);
        $counts = array_count_values($numbers);
        arsort($counts);
        return array_slice(array_keys($counts), 0, 3);
    }

    /**
     * Calculate lat lien tuc for a specific province.
     */
    protected function calculateLatLienTucForProvince(int $provinceId): ?string
    {
        $results = LotteryResult::where('province_id', $provinceId)
            ->orderBy('draw_date', 'desc')
            ->take(10)
            ->get();

        if ($results->count() < 5) {
            return null;
        }

        $appearances = [];
        foreach ($results as $result) {
            $allPrizes = $this->extractAllLotoNumbers($result);
            foreach ($allPrizes as $num) {
                $appearances[$num] = ($appearances[$num] ?? 0) + 1;
            }
        }

        // Find numbers appearing in ~50% of draws
        foreach ($appearances as $num => $count) {
            if ($count >= 4 && $count <= 6) {
                return $num;
            }
        }

        return null;
    }

    /**
     * Calculate pascal for a specific province.
     */
    protected function calculatePascalForProvince(int $provinceId): array
    {
        $results = LotteryResult::where('province_id', $provinceId)
            ->orderBy('draw_date', 'desc')
            ->take(5)
            ->pluck('prize_special')
            ->filter()
            ->map(fn($prize) => (int) substr($prize, -2))
            ->toArray();

        if (count($results) < 3) {
            return [];
        }

        $row = array_values($results);
        while (count($row) > 1) {
            $newRow = [];
            for ($i = 0; $i < count($row) - 1; $i++) {
                $sum = ($row[$i] + $row[$i + 1]) % 10;
                $newRow[] = $sum;
            }
            $row = $newRow;
        }

        if (!empty($row)) {
            $base = $row[0];
            return [
                str_pad($base, 2, '0', STR_PAD_LEFT),
                str_pad(($base + 5) % 10, 2, '0', STR_PAD_LEFT),
            ];
        }

        return [];
    }

    /**
     * Calculate lo kep from a single lottery result.
     */
    protected function calculateLoKepFromResult(LotteryResult $result): ?string
    {
        $allNumbers = $this->extractAllLotoNumbers($result);
        foreach ($allNumbers as $num) {
            if (strlen($num) == 2 && $num[0] == $num[1]) {
                return $num;
            }
        }
        return null;
    }
}
