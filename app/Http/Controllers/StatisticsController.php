<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\LotteryResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    /**
     * Statistics hub page
     */
    public function index()
    {
        // Get provinces for sidebar
        $provinces = Province::where('is_active', true)->get();
        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        return view('statistics.index', compact(
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Frequency statistics page
     */
    public function frequency(Request $request)
    {
        $provinceId = $request->input('province_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $selectedNumbers = $request->input('numbers', []);

        // Get provinces for dropdown and sidebar
        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        // Default dates
        if (!$endDate) {
            $endDate = Carbon::today()->format('Y-m-d');
        }
        if (!$startDate) {
            $startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        }

        $frequencyData = [];
        $dates = [];
        $selectedProvince = null;

        if ($provinceId && count($selectedNumbers) > 0) {
            $selectedProvince = Province::find($provinceId);

            // Get results for date range
            $results = LotteryResult::where('province_id', $provinceId)
                ->whereBetween('draw_date', [$startDate, $endDate])
                ->orderBy('draw_date', 'asc')
                ->get();

            // Build frequency matrix
            foreach ($results as $result) {
                $dateKey = $result->draw_date->format('d/m');
                $dates[$dateKey] = $result->draw_date->format('d/m/Y');

                // Extract loto numbers from result
                $lotoNumbers = $this->extractLotoNumbers($result);

                foreach ($selectedNumbers as $num) {
                    if (!isset($frequencyData[$num])) {
                        $frequencyData[$num] = [];
                    }
                    $frequencyData[$num][$dateKey] = in_array($num, $lotoNumbers) ? 1 : 0;
                }
            }
        }

        return view('statistics.tan-suat', compact(
            'provinces',
            'selectedProvince',
            'frequencyData',
            'dates',
            'startDate',
            'endDate',
            'selectedNumbers',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Overdue (Loto Gan) statistics page
     */
    public function overdue(Request $request)
    {
        $provinceId = $request->input('province_id');
        $minGap = $request->input('min_gap', 0);
        $maxGap = $request->input('max_gap', 100);

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        $overdueData = [];
        $selectedProvince = null;

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);

            // Get last 200 results to calculate gaps
            $results = LotteryResult::where('province_id', $provinceId)
                ->orderBy('draw_date', 'desc')
                ->limit(200)
                ->get();

            // Track last appearance of each number
            $lastAppearance = [];
            for ($i = 0; $i <= 99; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $lastAppearance[$num] = null;
            }

            $drawIndex = 0;
            foreach ($results as $result) {
                $lotoNumbers = $this->extractLotoNumbers($result);
                foreach ($lotoNumbers as $num) {
                    if ($lastAppearance[$num] === null) {
                        $lastAppearance[$num] = [
                            'draws_ago' => $drawIndex,
                            'date' => $result->draw_date
                        ];
                    }
                }
                $drawIndex++;
            }

            // Build overdue data
            foreach ($lastAppearance as $num => $data) {
                $gap = $data ? $data['draws_ago'] : $drawIndex;
                if ($gap >= $minGap && $gap <= $maxGap) {
                    $overdueData[] = [
                        'number' => $num,
                        'gap' => $gap,
                        'last_date' => $data ? $data['date']->format('d/m/Y') : 'Chưa xuất hiện'
                    ];
                }
            }

            // Sort by gap descending
            usort($overdueData, fn($a, $b) => $b['gap'] <=> $a['gap']);
        }

        return view('statistics.loto-gan', compact(
            'provinces',
            'selectedProvince',
            'overdueData',
            'minGap',
            'maxGap',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Head/Tail statistics page
     */
    public function headTail(Request $request)
    {
        $provinceId = $request->input('province_id');
        $period = $request->input('period', 30);

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        $headStats = array_fill(0, 10, 0);
        $tailStats = array_fill(0, 10, 0);
        $selectedProvince = null;
        $totalDraws = 0;

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);
            $startDate = Carbon::today()->subDays($period);

            $results = LotteryResult::where('province_id', $provinceId)
                ->where('draw_date', '>=', $startDate)
                ->get();

            $totalDraws = $results->count();

            foreach ($results as $result) {
                $lotoNumbers = $this->extractLotoNumbers($result);
                foreach ($lotoNumbers as $num) {
                    $head = (int) substr($num, 0, 1);
                    $tail = (int) substr($num, -1);
                    $headStats[$head]++;
                    $tailStats[$tail]++;
                }
            }
        }

        return view('statistics.dau-duoi', compact(
            'provinces',
            'selectedProvince',
            'headStats',
            'tailStats',
            'period',
            'totalDraws',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Quick statistics page (Thống kê nhanh)
     */
    public function quick(Request $request)
    {
        $provinceId = $request->input('province_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $numberGroup = $request->input('number_group', 'all');
        $prizeFilter = $request->input('prize_filter', 'all');

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        // Default dates
        if (!$endDate) {
            $endDate = Carbon::today()->format('Y-m-d');
        }
        if (!$startDate) {
            $startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        }

        $quickData = [];
        $selectedProvince = null;

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);

            // Get results for date range
            $results = LotteryResult::where('province_id', $provinceId)
                ->whereBetween('draw_date', [$startDate, $endDate])
                ->orderBy('draw_date', 'desc')
                ->get();

            // Track statistics for each number
            $numberStats = [];
            for ($i = 0; $i <= 99; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $numberStats[$num] = [
                    'number' => $num,
                    'last_date' => null,
                    'total_count' => 0,
                    'days_absent' => null,
                ];
            }

            foreach ($results as $result) {
                if ($prizeFilter === 'special') {
                    $lotoNumbers = $result->prize_special ? [str_pad(substr($result->prize_special, -2), 2, '0', STR_PAD_LEFT)] : [];
                } else {
                    $lotoNumbers = $this->extractLotoNumbers($result);
                }

                foreach ($lotoNumbers as $num) {
                    $numberStats[$num]['total_count']++;
                    if ($numberStats[$num]['last_date'] === null) {
                        $numberStats[$num]['last_date'] = $result->draw_date;
                    }
                }
            }

            // Calculate days absent
            $today = Carbon::today();
            foreach ($numberStats as $num => &$stats) {
                if ($stats['last_date']) {
                    $stats['days_absent'] = $stats['last_date']->diffInDays($today);
                    $stats['last_date'] = $stats['last_date']->format('d/m/Y');
                } else {
                    $stats['days_absent'] = 999;
                    $stats['last_date'] = 'Chưa xuất hiện';
                }
            }

            // Filter by number group
            if ($numberGroup !== 'all') {
                $groupStart = (int)$numberGroup;
                $groupEnd = $groupStart + 9;
                $numberStats = array_filter($numberStats, function($stats) use ($groupStart, $groupEnd) {
                    $num = (int)$stats['number'];
                    return $num >= $groupStart && $num <= $groupEnd;
                });
            }

            $quickData = array_values($numberStats);
        }

        return view('statistics.thong-ke-nhanh', compact(
            'provinces',
            'selectedProvince',
            'quickData',
            'startDate',
            'endDate',
            'numberGroup',
            'prizeFilter',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Statistics by sum page (Thống kê theo tổng)
     */
    public function bySum(Request $request)
    {
        $provinceId = $request->input('province_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sumFilter = $request->input('sum_filter', 'all');

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        // Default dates
        if (!$endDate) {
            $endDate = Carbon::today()->format('Y-m-d');
        }
        if (!$startDate) {
            $startDate = Carbon::today()->subDays(30)->format('Y-m-d');
        }

        // Define sum groups
        $sumGroups = [];
        for ($sum = 0; $sum <= 9; $sum++) {
            $numbers = [];
            for ($i = 0; $i <= 99; $i++) {
                $digit1 = intdiv($i, 10);
                $digit2 = $i % 10;
                if (($digit1 + $digit2) % 10 === $sum) {
                    $numbers[] = str_pad($i, 2, '0', STR_PAD_LEFT);
                }
            }
            $sumGroups[$sum] = $numbers;
        }

        $sumData = [];
        $selectedProvince = null;

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);

            // Get results for date range
            $results = LotteryResult::where('province_id', $provinceId)
                ->whereBetween('draw_date', [$startDate, $endDate])
                ->orderBy('draw_date', 'desc')
                ->get();

            // Initialize sum stats
            for ($sum = 0; $sum <= 9; $sum++) {
                $sumData[$sum] = [
                    'sum' => $sum,
                    'numbers' => $sumGroups[$sum],
                    'last_date' => null,
                    'total_count' => 0,
                    'days_absent' => null,
                ];
            }

            // Process results
            foreach ($results as $result) {
                $lotoNumbers = $this->extractLotoNumbers($result);
                foreach ($lotoNumbers as $num) {
                    $digit1 = intdiv((int)$num, 10);
                    $digit2 = (int)$num % 10;
                    $sum = ($digit1 + $digit2) % 10;

                    $sumData[$sum]['total_count']++;
                    if ($sumData[$sum]['last_date'] === null) {
                        $sumData[$sum]['last_date'] = $result->draw_date;
                    }
                }
            }

            // Calculate days absent
            $today = Carbon::today();
            foreach ($sumData as $sum => &$stats) {
                if ($stats['last_date']) {
                    $stats['days_absent'] = $stats['last_date']->diffInDays($today);
                    $stats['last_date'] = $stats['last_date']->format('d/m/Y');
                } else {
                    $stats['days_absent'] = 999;
                    $stats['last_date'] = 'Chưa xuất hiện';
                }
            }

            // Filter by sum if specified
            if ($sumFilter !== 'all') {
                $sumData = [$sumFilter => $sumData[(int)$sumFilter]];
            }
        }

        return view('statistics.theo-tong', compact(
            'provinces',
            'selectedProvince',
            'sumData',
            'sumGroups',
            'startDate',
            'endDate',
            'sumFilter',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Important statistics page (Thống kê quan trọng)
     */
    public function important(Request $request)
    {
        $provinceId = $request->input('province_id');
        $preset = $request->input('preset', 'most_frequent_27');

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        $importantData = [];
        $selectedProvince = null;
        $presetTitle = '';

        $presets = [
            'most_frequent_27' => '27 số về nhiều nhất 30 ngày',
            'absent_10_days' => 'Số vắng mặt 10+ ngày',
            'least_frequent_10' => '10 số về ít nhất',
            'consecutive' => 'Số về liên tiếp',
        ];

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);
            $presetTitle = $presets[$preset] ?? '';

            // Get results for last 30 days
            $startDate = Carbon::today()->subDays(30);
            $results = LotteryResult::where('province_id', $provinceId)
                ->where('draw_date', '>=', $startDate)
                ->orderBy('draw_date', 'desc')
                ->get();

            // Track statistics for each number
            $numberStats = [];
            for ($i = 0; $i <= 99; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $numberStats[$num] = [
                    'number' => $num,
                    'last_date' => null,
                    'total_count' => 0,
                    'days_absent' => 999,
                    'consecutive_days' => 0,
                ];
            }

            // Track consecutive appearances
            $lastDateWithNumber = [];

            foreach ($results as $result) {
                $lotoNumbers = $this->extractLotoNumbers($result);
                foreach ($lotoNumbers as $num) {
                    $numberStats[$num]['total_count']++;
                    if ($numberStats[$num]['last_date'] === null) {
                        $numberStats[$num]['last_date'] = $result->draw_date;
                    }
                }
            }

            // Calculate days absent and consecutive
            $today = Carbon::today();
            foreach ($numberStats as $num => &$stats) {
                if ($stats['last_date']) {
                    $stats['days_absent'] = $stats['last_date']->diffInDays($today);
                    $stats['last_date_display'] = $stats['last_date']->format('d/m/Y');
                } else {
                    $stats['days_absent'] = 999;
                    $stats['last_date_display'] = 'Chưa xuất hiện';
                }
            }

            // Calculate consecutive days
            $resultsByDate = $results->groupBy(function($r) {
                return $r->draw_date->format('Y-m-d');
            });
            $sortedDates = $resultsByDate->keys()->sort()->values();

            foreach ($numberStats as $num => &$stats) {
                $consecutive = 0;
                $maxConsecutive = 0;
                $prevDate = null;

                foreach ($sortedDates as $dateStr) {
                    $resultsForDate = $resultsByDate[$dateStr];
                    $appeared = false;
                    foreach ($resultsForDate as $result) {
                        $lotoNumbers = $this->extractLotoNumbers($result);
                        if (in_array($num, $lotoNumbers)) {
                            $appeared = true;
                            break;
                        }
                    }

                    if ($appeared) {
                        $consecutive++;
                        $maxConsecutive = max($maxConsecutive, $consecutive);
                    } else {
                        $consecutive = 0;
                    }
                }
                $stats['consecutive_days'] = $maxConsecutive;
            }

            // Apply preset filter
            switch ($preset) {
                case 'most_frequent_27':
                    usort($numberStats, fn($a, $b) => $b['total_count'] <=> $a['total_count']);
                    $importantData = array_slice($numberStats, 0, 27);
                    break;

                case 'absent_10_days':
                    $importantData = array_filter($numberStats, fn($s) => $s['days_absent'] >= 10);
                    usort($importantData, fn($a, $b) => $b['days_absent'] <=> $a['days_absent']);
                    $importantData = array_values($importantData);
                    break;

                case 'least_frequent_10':
                    usort($numberStats, fn($a, $b) => $a['total_count'] <=> $b['total_count']);
                    $importantData = array_slice($numberStats, 0, 10);
                    break;

                case 'consecutive':
                    $importantData = array_filter($numberStats, fn($s) => $s['consecutive_days'] >= 2);
                    usort($importantData, fn($a, $b) => $b['consecutive_days'] <=> $a['consecutive_days']);
                    $importantData = array_values($importantData);
                    break;
            }
        }

        return view('statistics.quan-trong', compact(
            'provinces',
            'selectedProvince',
            'importantData',
            'preset',
            'presets',
            'presetTitle',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Weekly special table page (Bảng đặc biệt tuần)
     */
    public function weeklySpecial(Request $request)
    {
        $provinceId = $request->input('province_id');
        $numWeeks = $request->input('num_weeks', 10);

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        $weeklyData = [];
        $selectedProvince = null;

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);

            // Calculate date range
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subWeeks($numWeeks);

            $results = LotteryResult::where('province_id', $provinceId)
                ->whereBetween('draw_date', [$startDate, $endDate])
                ->orderBy('draw_date', 'desc')
                ->get();

            // Group results by week
            $resultsByWeek = [];
            foreach ($results as $result) {
                $weekStart = $result->draw_date->copy()->startOfWeek(Carbon::MONDAY);
                $weekKey = $weekStart->format('Y-m-d');

                if (!isset($resultsByWeek[$weekKey])) {
                    $resultsByWeek[$weekKey] = [
                        'week_start' => $weekStart->format('d/m'),
                        'week_end' => $weekStart->copy()->endOfWeek(Carbon::SUNDAY)->format('d/m'),
                        'days' => array_fill(1, 7, null), // Mon=1, Sun=7
                    ];
                }

                $dayOfWeek = $result->draw_date->dayOfWeekIso; // 1=Mon, 7=Sun
                if ($result->prize_special) {
                    $resultsByWeek[$weekKey]['days'][$dayOfWeek] = str_pad(substr($result->prize_special, -2), 2, '0', STR_PAD_LEFT);
                }
            }

            // Sort by week (most recent first)
            krsort($resultsByWeek);
            $weeklyData = array_values($resultsByWeek);
        }

        return view('statistics.dac-biet-tuan', compact(
            'provinces',
            'selectedProvince',
            'weeklyData',
            'numWeeks',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Monthly special table page (Bảng đặc biệt tháng)
     */
    public function monthlySpecial(Request $request)
    {
        $provinceId = $request->input('province_id');
        $year = $request->input('year', Carbon::today()->year);
        $startMonth = $request->input('start_month', 1);
        $endMonth = $request->input('end_month', 12);

        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        $northProvinces = $provinces->where('region', 'north');
        $centralProvinces = $provinces->where('region', 'central');
        $southProvinces = $provinces->where('region', 'south');

        $monthlyData = [];
        $selectedProvince = null;
        $availableYears = range(Carbon::today()->year, Carbon::today()->year - 5);

        if ($provinceId) {
            $selectedProvince = Province::find($provinceId);

            // Get results for the year
            $startDate = Carbon::createFromDate($year, $startMonth, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $endMonth, 1)->endOfMonth();

            $results = LotteryResult::where('province_id', $provinceId)
                ->whereBetween('draw_date', [$startDate, $endDate])
                ->orderBy('draw_date', 'asc')
                ->get();

            // Initialize grid (days 1-31 x months)
            for ($day = 1; $day <= 31; $day++) {
                $monthlyData[$day] = [];
                for ($month = $startMonth; $month <= $endMonth; $month++) {
                    $monthlyData[$day][$month] = null;
                }
            }

            // Fill in data
            foreach ($results as $result) {
                $day = $result->draw_date->day;
                $month = $result->draw_date->month;

                if ($result->prize_special && $month >= $startMonth && $month <= $endMonth) {
                    $monthlyData[$day][$month] = str_pad(substr($result->prize_special, -2), 2, '0', STR_PAD_LEFT);
                }
            }
        }

        return view('statistics.dac-biet-thang', compact(
            'provinces',
            'selectedProvince',
            'monthlyData',
            'year',
            'startMonth',
            'endMonth',
            'availableYears',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * Extract loto numbers (last 2 digits) from a lottery result
     */
    private function extractLotoNumbers(LotteryResult $result): array
    {
        $numbers = [];

        // Special prize
        if ($result->prize_special) {
            $numbers[] = str_pad(substr($result->prize_special, -2), 2, '0', STR_PAD_LEFT);
        }

        // Prize 1
        if ($result->prize_1) {
            $numbers[] = str_pad(substr($result->prize_1, -2), 2, '0', STR_PAD_LEFT);
        }

        // Prize 2-7
        foreach (['prize_2', 'prize_3', 'prize_4', 'prize_5', 'prize_6', 'prize_7'] as $field) {
            if ($result->$field) {
                foreach (explode(',', $result->$field) as $prize) {
                    $prize = trim($prize);
                    if (strlen($prize) >= 2) {
                        $numbers[] = str_pad(substr($prize, -2), 2, '0', STR_PAD_LEFT);
                    }
                }
            }
        }

        // Prize 8
        if ($result->prize_8) {
            $numbers[] = str_pad($result->prize_8, 2, '0', STR_PAD_LEFT);
        }

        return $numbers;
    }
}
