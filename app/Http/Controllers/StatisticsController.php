<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\LotteryResult;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $provinceId = $request->input('province_id');
        $period = $request->input('period', '30'); // Default 30 days
        $sortBy = $request->input('sort_by', 'frequency'); // frequency, last_appeared, number
        $sortOrder = $request->input('sort_order', 'desc'); // asc, desc

        // Get provinces for dropdown
        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        // Default to first province if none selected
        if (!$provinceId && $provinces->count() > 0) {
            $provinceId = $provinces->first()->id;
        }

        $selectedProvince = Province::find($provinceId);
        $statistics = [];
        $totalDraws = 0;

        if ($selectedProvince) {
            // Calculate date range
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subDays((int)$period);

            // Get all results for the province in the period
            $results = LotteryResult::where('province_id', $provinceId)
                ->whereBetween('draw_date', [$startDate, $endDate])
                ->orderBy('draw_date', 'desc')
                ->get();

            $totalDraws = $results->count();

            // Calculate number frequency (00-99)
            $numberFrequency = [];
            $lastAppearance = [];

            // Initialize all numbers 00-99
            for ($i = 0; $i <= 99; $i++) {
                $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                $numberFrequency[$num] = 0;
                $lastAppearance[$num] = null;
            }

            // Count occurrences
            foreach ($results as $result) {
                $drawDate = $result->draw_date;

                // Extract all numbers from prizes
                $allNumbers = [];

                // Special prize (last 2 digits)
                if ($result->prize_special) {
                    $allNumbers[] = substr($result->prize_special, -2);
                }

                // Prize 1 (last 2 digits)
                if ($result->prize_1) {
                    $allNumbers[] = substr($result->prize_1, -2);
                }

                // Prize 2 (last 2 digits of each)
                if ($result->prize_2) {
                    $prizes = explode(',', $result->prize_2);
                    foreach ($prizes as $prize) {
                        $allNumbers[] = substr(trim($prize), -2);
                    }
                }

                // Prize 3-7 (similar processing)
                foreach (['prize_3', 'prize_4', 'prize_5', 'prize_6', 'prize_7'] as $prizeField) {
                    if ($result->$prizeField) {
                        $prizes = explode(',', $result->$prizeField);
                        foreach ($prizes as $prize) {
                            $cleanPrize = trim($prize);
                            if (strlen($cleanPrize) >= 2) {
                                $allNumbers[] = substr($cleanPrize, -2);
                            }
                        }
                    }
                }

                // Prize 8 (if exists, it's already 2 digits)
                if ($result->prize_8) {
                    $allNumbers[] = str_pad($result->prize_8, 2, '0', STR_PAD_LEFT);
                }

                // Count frequency and track last appearance
                foreach ($allNumbers as $num) {
                    $num = str_pad($num, 2, '0', STR_PAD_LEFT);
                    if (isset($numberFrequency[$num])) {
                        $numberFrequency[$num]++;
                        if ($lastAppearance[$num] === null || $drawDate->gt($lastAppearance[$num])) {
                            $lastAppearance[$num] = $drawDate;
                        }
                    }
                }
            }

            // Build statistics array
            foreach ($numberFrequency as $number => $frequency) {
                $lastDate = $lastAppearance[$number];
                $cycle = $lastDate ? Carbon::today()->diffInDays($lastDate) : null;

                $statistics[] = [
                    'number' => $number,
                    'frequency' => $frequency,
                    'last_appeared' => $lastDate,
                    'cycle' => $cycle,
                    'percentage' => $totalDraws > 0 ? round(($frequency / $totalDraws) * 100, 2) : 0,
                ];
            }

            // Sort statistics
            usort($statistics, function ($a, $b) use ($sortBy, $sortOrder) {
                $aVal = $a[$sortBy];
                $bVal = $b[$sortBy];

                if ($aVal === null && $bVal === null) return 0;
                if ($aVal === null) return 1;
                if ($bVal === null) return -1;

                $comparison = $aVal <=> $bVal;
                return $sortOrder === 'desc' ? -$comparison : $comparison;
            });
        }

        return view('statistics', compact(
            'provinces',
            'selectedProvince',
            'statistics',
            'period',
            'sortBy',
            'sortOrder',
            'totalDraws'
        ));
    }
}
