<?php

namespace App\Http\Controllers;

use App\Models\LotteryResult;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LotteryController extends Controller
{
    public function xsmb(Request $request, $date = null)
    {
        // Priority: route parameter > query string > today
        if ($date) {
            // Parse from route parameter (DD-MM-YYYY format)
            try {
                $date = Carbon::createFromFormat('d-m-Y', $date);
            } catch (\Exception $e) {
                abort(404, 'Ngày không hợp lệ');
            }
        } else if ($request->has('date')) {
            // Backward compatibility: query string (Y-m-d format)
            $date = Carbon::parse($request->get('date'));
        } else {
            // Default to today
            $date = Carbon::today();
        }
        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek; // Convert Sunday from 0 to 7

        // Get provinces that draw on this day
        $provinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        $results = [];
        foreach ($provinces as $province) {
            $result = LotteryResult::where('province_id', $province->id)
                ->whereDate('draw_date', $date)
                ->first();
            if ($result) {
                $results[] = $result;
            }
        }

        return view('xsmb', compact('results', 'date', 'provinces'));
    }

    public function xsmt(Request $request, $date = null)
    {
        // Priority: route parameter > query string > today
        if ($date) {
            // Parse from route parameter (DD-MM-YYYY format)
            try {
                $date = Carbon::createFromFormat('d-m-Y', $date);
            } catch (\Exception $e) {
                abort(404, 'Ngày không hợp lệ');
            }
        } else if ($request->has('date')) {
            // Backward compatibility: query string (Y-m-d format)
            $date = Carbon::parse($request->get('date'));
        } else {
            // Default to today
            $date = Carbon::today();
        }
        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek; // Convert Sunday from 0 to 7

        // Get provinces that draw on this day
        $provinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        $results = [];
        foreach ($provinces as $province) {
            $result = LotteryResult::where('province_id', $province->id)
                ->whereDate('draw_date', $date)
                ->first();
            if ($result) {
                $results[] = $result;
            }
        }

        return view('xsmt', compact('results', 'date', 'provinces'));
    }

    public function xsmn(Request $request, $date = null)
    {
        // Priority: route parameter > query string > today
        if ($date) {
            // Parse from route parameter (DD-MM-YYYY format)
            try {
                $date = Carbon::createFromFormat('d-m-Y', $date);
            } catch (\Exception $e) {
                abort(404, 'Ngày không hợp lệ');
            }
        } else if ($request->has('date')) {
            // Backward compatibility: query string (Y-m-d format)
            $date = Carbon::parse($request->get('date'));
        } else {
            // Default to today
            $date = Carbon::today();
        }
        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek;

        // Get provinces that draw on this day
        $provinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        $results = [];
        foreach ($provinces as $province) {
            $result = LotteryResult::where('province_id', $province->id)
                ->whereDate('draw_date', $date)
                ->first();
            if ($result) {
                $results[] = $result;
            }
        }

        return view('xsmn', compact('results', 'date', 'provinces'));
    }

    public function resultsByDayOfWeek($region, $day)
    {
        // Map slug to day number (Carbon: 0=Sun, 1=Mon, ..., 6=Sat)
        $dayMap = [
            'thu-2' => 1,  // Monday
            'thu-3' => 2,  // Tuesday
            'thu-4' => 3,  // Wednesday
            'thu-5' => 4,  // Thursday
            'thu-6' => 5,  // Friday
            'thu-7' => 6,  // Saturday
            'chu-nhat' => 0,  // Sunday
        ];

        $dayOfWeek = $dayMap[$day] ?? null;
        if ($dayOfWeek === null) {
            abort(404);
        }

        // Find most recent date matching this day of week
        $date = Carbon::today();
        while ($date->dayOfWeek != $dayOfWeek) {
            $date->subDay();
        }

        // Call the existing region method with the calculated date
        return $this->{$region}(request(), $date->format('d-m-Y'));
    }

    public function provinceDetail($region, $slug)
    {
        $province = Province::where('slug', $slug)->firstOrFail();

        $results = LotteryResult::where('province_id', $province->id)
            ->orderBy('draw_date', 'desc')
            ->paginate(20);

        return view('province-detail', compact('province', 'results'));
    }

    public function loadMoreResults($region, $date)
    {
        // Validate region
        $regionMap = [
            'xsmb' => 'north',
            'xsmt' => 'central',
            'xsmn' => 'south',
        ];

        if (!isset($regionMap[$region])) {
            return response()->json(['error' => 'Invalid region'], 400);
        }

        // Parse date (DD-MM-YYYY format)
        try {
            $date = Carbon::createFromFormat('d-m-Y', $date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }

        // Check 30-day limit
        $thirtyDaysAgo = Carbon::today()->subDays(30);
        if ($date->lt($thirtyDaysAgo)) {
            return response()->json([
                'html' => '',
                'nextDate' => null,
                'hasMore' => false,
                'message' => 'Đã hiển thị tất cả kết quả trong 30 ngày qua'
            ]);
        }

        $dbRegion = $regionMap[$region];
        $results = [];
        $currentDate = $date->copy();
        $attempts = 0;
        $maxAttempts = 7; // Try up to 7 days to find results

        // Keep looking for results until we find some or hit limits
        while (empty($results) && $attempts < $maxAttempts && $currentDate->gte($thirtyDaysAgo)) {
            $dayOfWeek = $currentDate->dayOfWeek == 0 ? 7 : $currentDate->dayOfWeek;

            // Get provinces that draw on this day
            $provinces = Province::where('region', $dbRegion)
                ->where('is_active', true)
                ->get()
                ->filter(function ($province) use ($dayOfWeek) {
                    return in_array($dayOfWeek, $province->draw_days ?? []);
                });

            foreach ($provinces as $province) {
                $result = LotteryResult::where('province_id', $province->id)
                    ->whereDate('draw_date', $currentDate)
                    ->first();
                if ($result) {
                    $results[] = $result;
                }
            }

            if (empty($results)) {
                $currentDate->subDay();
                $attempts++;
            }
        }

        // Calculate next date
        $nextDate = $currentDate->copy()->subDay();
        $hasMore = $nextDate->gte($thirtyDaysAgo);

        // Render HTML
        $html = '';
        if (!empty($results)) {
            $html = view('partials.results-list', [
                'results' => $results,
                'region' => $region
            ])->render();
        }

        return response()->json([
            'html' => $html,
            'nextDate' => $nextDate->format('d-m-Y'),
            'currentDate' => $currentDate->format('d/m/Y'),
            'hasMore' => $hasMore,
            'resultsCount' => count($results)
        ]);
    }
}
