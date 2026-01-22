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
        $isSpecificDate = false;

        // Priority: route parameter > query string > today
        if ($date) {
            // Parse from route parameter (DD-MM-YYYY format)
            try {
                $date = Carbon::createFromFormat('d-m-Y', $date);
                $isSpecificDate = true;
            } catch (\Exception $e) {
                abort(404, 'Ngày không hợp lệ');
            }
        } else if ($request->has('date')) {
            // Backward compatibility: query string (Y-m-d format)
            $date = Carbon::parse($request->get('date'));
            $isSpecificDate = true;
        } else {
            // Default to today
            $date = Carbon::today();
        }

        // Get Hà Nội province (for XSMB, all provinces have same results, so we only show Hà Nội)
        $hanoiProvince = Province::where('slug', 'ha-noi')->first();

        // Get provinces for the selected date (for display purposes)
        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek;
        $provinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        if ($isSpecificDate) {
            // Specific date: show only Hà Nội's result for that date
            $results = [];
            if ($hanoiProvince) {
                $result = LotteryResult::where('province_id', $hanoiProvince->id)
                    ->whereDate('draw_date', $date)
                    ->first();
                if ($result) {
                    $results[] = $result;
                }
            }
            $nextDate = $date->copy()->subDay();
        } else {
            // Default page: fetch 5 result cards going back from today (only Hà Nội)
            $fetchResult = $this->fetchMultipleResults('north', $date, 5, 'ha-noi');
            $results = $fetchResult['results'];
            $nextDate = $fetchResult['nextDate'];
        }

        // Add province lists for sidebar
        $northProvinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $centralProvinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $southProvinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('xsmb', compact(
            'results',
            'date',
            'provinces',
            'northProvinces',
            'centralProvinces',
            'southProvinces',
            'nextDate',
            'isSpecificDate'
        ));
    }

    /**
     * Fetch multiple result cards going back from a starting date
     *
     * @param string $dbRegion The region (north, central, south)
     * @param Carbon $startDate The starting date to fetch from
     * @param int $count Number of results to fetch
     * @param string|null $provinceSlug Optional province slug to filter by (e.g., 'ha-noi' for XSMB)
     */
    private function fetchMultipleResults($dbRegion, Carbon $startDate, $count = 5, $provinceSlug = null)
    {
        $results = [];
        $currentDate = $startDate->copy();
        $thirtyDaysAgo = Carbon::today()->subDays(30);

        // If a specific province is requested, get it
        $specificProvince = null;
        if ($provinceSlug) {
            $specificProvince = Province::where('slug', $provinceSlug)->first();
        }

        while (count($results) < $count && $currentDate->gte($thirtyDaysAgo)) {
            $dayOfWeek = $currentDate->dayOfWeek == 0 ? 7 : $currentDate->dayOfWeek;

            if ($specificProvince) {
                // Fetch only for the specific province
                if (in_array($dayOfWeek, $specificProvince->draw_days ?? [])) {
                    $result = LotteryResult::where('province_id', $specificProvince->id)
                        ->whereDate('draw_date', $currentDate)
                        ->first();
                    if ($result) {
                        $results[] = $result;
                    }
                }
            } else {
                // Get all provinces that draw on this day
                $provinces = Province::where('region', $dbRegion)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
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
            }

            $currentDate->subDay();
        }

        return [
            'results' => $results,
            'nextDate' => $currentDate, // This is already one day before the last processed date
        ];
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
            ->paginate(10);

        // Get province lists for sidebar
        $northProvinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $centralProvinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $southProvinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('province-detail', compact(
            'province',
            'results',
            'region',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    public function loadMoreProvinceResults($region, $slug, $page)
    {
        $province = Province::where('slug', $slug)->first();

        if (!$province) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        $perPage = 10;
        $results = LotteryResult::where('province_id', $province->id)
            ->orderBy('draw_date', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Calculate if there are more results
        $totalResults = LotteryResult::where('province_id', $province->id)->count();
        $hasMore = ($page * $perPage) < $totalResults;

        // Render HTML
        $html = '';
        if ($results->count() > 0) {
            $html = view('partials.province-results-list', [
                'results' => $results,
                'region' => $region
            ])->render();
        }

        return response()->json([
            'html' => $html,
            'hasMore' => $hasMore,
            'nextPage' => $hasMore ? $page + 1 : null,
            'resultsCount' => $results->count()
        ]);
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

        // For XSMB (north), only fetch Hà Nội results to avoid duplicates
        $provinceSlug = ($region === 'xsmb') ? 'ha-noi' : null;

        // Fetch 5 result cards
        $fetchResult = $this->fetchMultipleResults($dbRegion, $date, 5, $provinceSlug);
        $results = $fetchResult['results'];
        $nextDate = $fetchResult['nextDate'];

        // Check if there are more results available
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
            'hasMore' => $hasMore,
            'resultsCount' => count($results)
        ]);
    }
}
