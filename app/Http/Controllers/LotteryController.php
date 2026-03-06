<?php

namespace App\Http\Controllers;

use App\Models\LotteryResult;
use App\Models\Province;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

        $dayLabel = $request->attributes->get('dayLabel');

        return view('xsmb', compact(
            'results',
            'date',
            'provinces',
            'northProvinces',
            'centralProvinces',
            'southProvinces',
            'nextDate',
            'isSpecificDate',
            'dayLabel'
        ));
    }

    /**
     * Fetch grouped results for a single date (all provinces for that day)
     *
     * @param string $dbRegion The region (north, central, south)
     * @param Carbon $date The date to fetch results for
     * @return array|null Returns array with date, dayOfWeek, provinces, results or null if no results
     */
    private function fetchGroupedResultsForDate($dbRegion, Carbon $date)
    {
        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek;

        // Get provinces that draw on this day
        $provinces = Province::where('region', $dbRegion)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        if ($provinces->isEmpty()) {
            return null;
        }

        // Fetch results for all provinces on this date
        $results = [];
        foreach ($provinces as $province) {
            $result = LotteryResult::where('province_id', $province->id)
                ->whereDate('draw_date', $date)
                ->first();
            if ($result) {
                $results[$province->id] = $result;
            }
        }

        if (empty($results)) {
            return null;
        }

        return [
            'date' => $date->copy(),
            'dayOfWeek' => $dayOfWeek,
            'provinces' => $provinces,
            'results' => $results,
        ];
    }

    /**
     * Fetch multiple days of grouped results going back from a starting date
     *
     * @param string $dbRegion The region (north, central, south)
     * @param Carbon $startDate The starting date to fetch from
     * @param int $count Number of days with results to fetch
     * @return array Contains 'groupedResults' array and 'nextDate' Carbon instance
     */
    private function fetchMultipleGroupedResults($dbRegion, Carbon $startDate, $count = 5)
    {
        $groupedResults = [];
        $currentDate = $startDate->copy();
        $thirtyDaysAgo = Carbon::today()->subDays(30);

        while (count($groupedResults) < $count && $currentDate->gte($thirtyDaysAgo)) {
            $dayGroup = $this->fetchGroupedResultsForDate($dbRegion, $currentDate);

            if ($dayGroup !== null) {
                $groupedResults[] = $dayGroup;
            }

            $currentDate->subDay();
        }

        return [
            'groupedResults' => $groupedResults,
            'nextDate' => $currentDate,
        ];
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

        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek;

        // Get provinces that draw on the selected day (for display purposes)
        $provinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        if ($isSpecificDate) {
            // Specific date: show only that day's grouped results
            $dayGroup = $this->fetchGroupedResultsForDate('central', $date);
            $groupedResults = $dayGroup ? [$dayGroup] : [];
            $nextDate = $date->copy()->subDay();
        } else {
            // Default page: fetch 5 days of grouped results
            $fetchResult = $this->fetchMultipleGroupedResults('central', $date, 5);
            $groupedResults = $fetchResult['groupedResults'];
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

        $dayLabel = $request->attributes->get('dayLabel');

        return view('xsmt', compact(
            'groupedResults',
            'date',
            'provinces',
            'northProvinces',
            'centralProvinces',
            'southProvinces',
            'nextDate',
            'isSpecificDate',
            'dayLabel'
        ));
    }

    public function xsmn(Request $request, $date = null)
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

        $dayOfWeek = $date->dayOfWeek == 0 ? 7 : $date->dayOfWeek;

        // Get provinces that draw on the selected day (for display purposes)
        $provinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->filter(function ($province) use ($dayOfWeek) {
                return in_array($dayOfWeek, $province->draw_days ?? []);
            });

        if ($isSpecificDate) {
            // Specific date: show only that day's grouped results
            $dayGroup = $this->fetchGroupedResultsForDate('south', $date);
            $groupedResults = $dayGroup ? [$dayGroup] : [];
            $nextDate = $date->copy()->subDay();
        } else {
            // Default page: fetch 5 days of grouped results
            $fetchResult = $this->fetchMultipleGroupedResults('south', $date, 5);
            $groupedResults = $fetchResult['groupedResults'];
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

        $dayLabel = $request->attributes->get('dayLabel');

        return view('xsmn', compact(
            'groupedResults',
            'date',
            'provinces',
            'northProvinces',
            'centralProvinces',
            'southProvinces',
            'nextDate',
            'isSpecificDate',
            'dayLabel'
        ));
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

        $dayLabelMap = [
            'thu-2' => 'Thứ 2',
            'thu-3' => 'Thứ 3',
            'thu-4' => 'Thứ 4',
            'thu-5' => 'Thứ 5',
            'thu-6' => 'Thứ 6',
            'thu-7' => 'Thứ 7',
            'chu-nhat' => 'Chủ Nhật',
        ];

        // Store day label so the view can use it for the page title
        request()->attributes->set('dayLabel', $dayLabelMap[$day]);

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

        // Special handling for XSMT and XSMN - use grouped results
        if ($region === 'xsmt' || $region === 'xsmn') {
            // Fetch 5 days of grouped results
            $fetchResult = $this->fetchMultipleGroupedResults($dbRegion, $date, 5);
            $groupedResults = $fetchResult['groupedResults'];
            $nextDate = $fetchResult['nextDate'];

            // Check if there are more results available
            $hasMore = $nextDate->gte($thirtyDaysAgo);

            // Render HTML with appropriate partial
            $html = '';
            if (!empty($groupedResults)) {
                $partialName = $region === 'xsmt' ? 'partials.xsmt-grouped-results-list' : 'partials.xsmn-grouped-results-list';
                $html = view($partialName, [
                    'groupedResults' => $groupedResults,
                    'region' => $region
                ])->render();
            }

            return response()->json([
                'html' => $html,
                'nextDate' => $nextDate->format('d-m-Y'),
                'hasMore' => $hasMore,
                'resultsCount' => count($groupedResults),
                'currentDate' => $date->format('d-m-Y')
            ]);
        }

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
            'resultsCount' => count($results),
            'currentDate' => $date->format('d-m-Y')
        ]);
    }

    public function xsmbLive()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $startTime = $now->copy()->setTime(18, 15, 0);
        $endTime = $now->copy()->setTime(18, 50, 0);

        if ($now->lt($startTime)) {
            $sessionState = 'before';
        } elseif ($now->lte($endTime)) {
            $sessionState = 'live';
        } else {
            $sessionState = 'after';
        }

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

        return view('xsmb-live', compact(
            'sessionState',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    public function fetchLiveResults()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $date = $now->format('Y-m-d');
        $cacheKey = 'xsmb_live_results_' . $date;

        $data = Cache::remember($cacheKey, 30, function () use ($now) {
            try {
                $response = Http::timeout(10)->get('https://xskt.com.vn/hom-nay/');

                if (!$response->successful()) {
                    return [
                        'status' => 'error',
                        'prizes' => $this->emptyPrizes(),
                        'timestamp' => $now->toIso8601String(),
                        'is_previous_day' => false,
                    ];
                }

                $parsed = $this->parseXsmbLiveHtml($response->body());

                $todayStr = $now->format('d-m-Y');
                $resultsDate = $parsed['results_date']; // dd-mm-yyyy
                $isPreviousDay = $resultsDate && $resultsDate !== $todayStr;

                // Build display strings for the frontend note
                $resultsDateDisplay = null;
                $todayDateDisplay = null;
                if ($isPreviousDay && $resultsDate) {
                    $resultsCarbon = Carbon::createFromFormat('d-m-Y', $resultsDate, 'Asia/Ho_Chi_Minh');
                    $todayCarbon = $now->copy();

                    $dayNames = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                    $resultsDateDisplay = $dayNames[$resultsCarbon->dayOfWeek] . ' ngày ' . $resultsCarbon->format('d/m');
                    $todayDateDisplay = $dayNames[$todayCarbon->dayOfWeek] . ' ngày ' . $todayCarbon->format('d/m');
                }

                return [
                    'status' => $parsed['complete'] ? 'complete' : 'in_progress',
                    'prizes' => $parsed['prizes'],
                    'timestamp' => $now->toIso8601String(),
                    'results_date' => $resultsDate,
                    'is_previous_day' => $isPreviousDay,
                    'results_date_display' => $resultsDateDisplay,
                    'today_date_display' => $todayDateDisplay,
                ];
            } catch (\Exception $e) {
                Log::warning('Live XSMB scrape failed: ' . $e->getMessage());

                return [
                    'status' => 'error',
                    'prizes' => $this->emptyPrizes(),
                    'timestamp' => $now->toIso8601String(),
                    'is_previous_day' => false,
                ];
            }
        });

        return response()->json($data);
    }

    private function parseXsmbLiveHtml(string $html): array
    {
        $prizes = $this->emptyPrizes();
        $complete = false;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Find the first table.result inside .box-ketqua
        $tables = $xpath->query("//*[contains(@class, 'box-ketqua')]//table[contains(@class, 'result')]");
        if ($tables->length === 0) {
            // Fallback: try just table.result
            $tables = $xpath->query("//table[contains(@class, 'result')]");
        }

        if ($tables->length === 0) {
            return ['prizes' => $prizes, 'complete' => false];
        }

        $table = $tables->item(0);
        $rows = $xpath->query('.//tr', $table);

        $prizeMap = [
            'ĐB' => 'prize_special',
            'DB' => 'prize_special',
            'Đ.Biệt' => 'prize_special',
            'G.1' => 'prize_1',
            'G1' => 'prize_1',
            'Giải nhất' => 'prize_1',
            'G.2' => 'prize_2',
            'G2' => 'prize_2',
            'Giải nhì' => 'prize_2',
            'G.3' => 'prize_3',
            'G3' => 'prize_3',
            'Giải ba' => 'prize_3',
            'G.4' => 'prize_4',
            'G4' => 'prize_4',
            'Giải tư' => 'prize_4',
            'G.5' => 'prize_5',
            'G5' => 'prize_5',
            'Giải năm' => 'prize_5',
            'G.6' => 'prize_6',
            'G6' => 'prize_6',
            'Giải sáu' => 'prize_6',
            'G.7' => 'prize_7',
            'G7' => 'prize_7',
            'Giải bảy' => 'prize_7',
        ];

        foreach ($rows as $row) {
            $cells = $xpath->query('.//td', $row);
            if ($cells->length < 2) continue;

            $label = trim($cells->item(0)->textContent);
            $prizeKey = $prizeMap[$label] ?? null;
            // Skip rows without a recognized prize label (e.g. continuation rows from rowspan,
            // which only contain Đầu/Đuôi columns, or the footer row with mã ĐB)
            if (!$prizeKey) continue;

            // Only read cell at index 1 (the prize numbers cell).
            // Cells at index 2+ are Đầu/Đuôi loto summary columns — skip them.
            // Get innerHTML and replace <br> with space so numbers aren't concatenated.
            $cell = $cells->item(1);
            $innerHtml = '';
            foreach ($cell->childNodes as $child) {
                $innerHtml .= $dom->saveHTML($child);
            }
            $cellText = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $innerHtml)));
            $numbers = [];
            $parts = preg_split('/[\s,;]+/', $cellText);
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part === '' || $part === '...' || $part === '---' || $part === '____') {
                    continue;
                }
                if (preg_match('/^\d+$/', $part)) {
                    $numbers[] = $part;
                }
            }

            if (!empty($numbers)) {
                if ($prizes[$prizeKey] !== null) {
                    $prizes[$prizeKey] .= ',' . implode(',', $numbers);
                } else {
                    $prizes[$prizeKey] = implode(',', $numbers);
                }
            }
        }

        // Check completion: all prizes must be non-null
        $complete = true;
        foreach ($prizes as $value) {
            if ($value === null) {
                $complete = false;
                break;
            }
        }

        // Extract XSMB result date from the link "/xsmb/ngay-D-M-YYYY" in the box-ketqua h2
        $resultsDate = null;
        preg_match('/\/xsmb\/ngay-(\d{1,2})-(\d{1,2})-(\d{4})/', $html, $dateMatch);
        if ($dateMatch) {
            $resultsDate = sprintf('%s-%s-%s',
                str_pad($dateMatch[1], 2, '0', STR_PAD_LEFT),
                str_pad($dateMatch[2], 2, '0', STR_PAD_LEFT),
                $dateMatch[3]
            );
        }

        return ['prizes' => $prizes, 'complete' => $complete, 'results_date' => $resultsDate];
    }

    private function emptyPrizes(): array
    {
        return [
            'prize_special' => null,
            'prize_1' => null,
            'prize_2' => null,
            'prize_3' => null,
            'prize_4' => null,
            'prize_5' => null,
            'prize_6' => null,
            'prize_7' => null,
        ];
    }

    private function emptyXsmnPrizes(): array
    {
        return [
            'prize_8' => null,
            'prize_7' => null,
            'prize_6' => null,
            'prize_5' => null,
            'prize_4' => null,
            'prize_3' => null,
            'prize_2' => null,
            'prize_1' => null,
            'prize_special' => null,
        ];
    }

    public function xsmnLive()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $startTime = $now->copy()->setTime(16, 15, 0);
        $endTime = $now->copy()->setTime(16, 50, 0);

        if ($now->lt($startTime)) {
            $sessionState = 'before';
        } elseif ($now->lte($endTime)) {
            $sessionState = 'live';
        } else {
            $sessionState = 'after';
        }

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

        return view('xsmn-live', compact(
            'sessionState',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    public function fetchXsmnLiveResults()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $date = $now->format('Y-m-d');
        $cacheKey = 'xsmn_live_results_' . $date;

        $data = Cache::remember($cacheKey, 30, function () use ($now) {
            try {
                $response = Http::timeout(10)->get('https://xskt.com.vn/hom-nay/');

                if (!$response->successful()) {
                    return [
                        'status' => 'error',
                        'provinces' => [],
                        'timestamp' => $now->toIso8601String(),
                        'is_previous_day' => false,
                    ];
                }

                $parsed = $this->parseXsmnLiveHtml($response->body());

                $todayStr = $now->format('d-m-Y');
                $resultsDate = $parsed['results_date'];
                $isPreviousDay = $resultsDate && $resultsDate !== $todayStr;

                $resultsDateDisplay = null;
                $todayDateDisplay = null;
                if ($isPreviousDay && $resultsDate) {
                    $resultsCarbon = Carbon::createFromFormat('d-m-Y', $resultsDate, 'Asia/Ho_Chi_Minh');
                    $todayCarbon = $now->copy();

                    $dayNames = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                    $resultsDateDisplay = $dayNames[$resultsCarbon->dayOfWeek] . ' ngày ' . $resultsCarbon->format('d/m');
                    $todayDateDisplay = $dayNames[$todayCarbon->dayOfWeek] . ' ngày ' . $todayCarbon->format('d/m');
                }

                return [
                    'status' => $parsed['complete'] ? 'complete' : 'in_progress',
                    'provinces' => $parsed['provinces'],
                    'timestamp' => $now->toIso8601String(),
                    'results_date' => $resultsDate,
                    'is_previous_day' => $isPreviousDay,
                    'results_date_display' => $resultsDateDisplay,
                    'today_date_display' => $todayDateDisplay,
                ];
            } catch (\Exception $e) {
                Log::warning('Live XSMN scrape failed: ' . $e->getMessage());

                return [
                    'status' => 'error',
                    'provinces' => [],
                    'timestamp' => $now->toIso8601String(),
                    'is_previous_day' => false,
                ];
            }
        });

        return response()->json($data);
    }

    public function xsmtLive()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $startTime = $now->copy()->setTime(17, 15, 0);
        $endTime = $now->copy()->setTime(17, 50, 0);

        if ($now->lt($startTime)) {
            $sessionState = 'before';
        } elseif ($now->lte($endTime)) {
            $sessionState = 'live';
        } else {
            $sessionState = 'after';
        }

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

        return view('xsmt-live', compact(
            'sessionState',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    public function fetchXsmtLiveResults()
    {
        $now = Carbon::now('Asia/Ho_Chi_Minh');
        $date = $now->format('Y-m-d');
        $cacheKey = 'xsmt_live_results_' . $date;

        $data = Cache::remember($cacheKey, 30, function () use ($now) {
            try {
                $response = Http::timeout(10)->get('https://xskt.com.vn/hom-nay/');

                if (!$response->successful()) {
                    return [
                        'status' => 'error',
                        'provinces' => [],
                        'timestamp' => $now->toIso8601String(),
                        'is_previous_day' => false,
                    ];
                }

                $parsed = $this->parseXsmtLiveHtml($response->body());

                $todayStr = $now->format('d-m-Y');
                $resultsDate = $parsed['results_date'];
                $isPreviousDay = $resultsDate && $resultsDate !== $todayStr;

                $resultsDateDisplay = null;
                $todayDateDisplay = null;
                if ($isPreviousDay && $resultsDate) {
                    $resultsCarbon = Carbon::createFromFormat('d-m-Y', $resultsDate, 'Asia/Ho_Chi_Minh');
                    $todayCarbon = $now->copy();

                    $dayNames = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
                    $resultsDateDisplay = $dayNames[$resultsCarbon->dayOfWeek] . ' ngày ' . $resultsCarbon->format('d/m');
                    $todayDateDisplay = $dayNames[$todayCarbon->dayOfWeek] . ' ngày ' . $todayCarbon->format('d/m');
                }

                return [
                    'status' => $parsed['complete'] ? 'complete' : 'in_progress',
                    'provinces' => $parsed['provinces'],
                    'timestamp' => $now->toIso8601String(),
                    'results_date' => $resultsDate,
                    'is_previous_day' => $isPreviousDay,
                    'results_date_display' => $resultsDateDisplay,
                    'today_date_display' => $todayDateDisplay,
                ];
            } catch (\Exception $e) {
                Log::warning('Live XSMT scrape failed: ' . $e->getMessage());

                return [
                    'status' => 'error',
                    'provinces' => [],
                    'timestamp' => $now->toIso8601String(),
                    'is_previous_day' => false,
                ];
            }
        });

        return response()->json($data);
    }

    private function parseXsmtLiveHtml(string $html): array
    {
        $provinces = [];
        $complete = false;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Find the XSMT table: use id starting with MT (NOT class tbl-xsmn which matches both regions)
        $tables = $xpath->query("//table[starts-with(@id, 'MT')]");
        if ($tables->length === 0) {
            // Fallback: find table inside div whose id starts with MT
            $tables = $xpath->query("//*[starts-with(@id, 'MT')]//table");
        }

        if ($tables->length === 0) {
            return ['provinces' => [], 'complete' => false, 'results_date' => null];
        }

        $table = $tables->item(0);

        // Extract province names from header row <th> elements
        $headerRow = $xpath->query('.//thead/tr | .//tr', $table);
        $provinceNames = [];

        if ($headerRow->length > 0) {
            $ths = $xpath->query('.//th', $headerRow->item(0));
            // Skip the first <th> (day/date label), rest are province names
            for ($i = 1; $i < $ths->length; $i++) {
                $name = trim($ths->item($i)->textContent);
                if ($name !== '') {
                    $slug = \Illuminate\Support\Str::slug($name);
                    $provinceNames[] = ['name' => $name, 'slug' => $slug];
                }
            }
        }

        if (empty($provinceNames)) {
            return ['provinces' => [], 'complete' => false, 'results_date' => null];
        }

        // Initialize prizes for each province
        foreach ($provinceNames as &$prov) {
            $prov['prizes'] = $this->emptyXsmnPrizes();
        }
        unset($prov);

        // Map row labels to prize keys
        $prizeMap = [
            'G.8' => 'prize_8',
            'G8' => 'prize_8',
            'G.7' => 'prize_7',
            'G7' => 'prize_7',
            'G.6' => 'prize_6',
            'G6' => 'prize_6',
            'G.5' => 'prize_5',
            'G5' => 'prize_5',
            'G.4' => 'prize_4',
            'G4' => 'prize_4',
            'G.3' => 'prize_3',
            'G3' => 'prize_3',
            'G.2' => 'prize_2',
            'G2' => 'prize_2',
            'G.1' => 'prize_1',
            'G1' => 'prize_1',
            'ĐB' => 'prize_special',
            'DB' => 'prize_special',
            'Đ.Biệt' => 'prize_special',
        ];

        // Parse rows
        $rows = $xpath->query('.//tr', $table);
        foreach ($rows as $row) {
            $cells = $xpath->query('.//td', $row);
            if ($cells->length < 2) continue;

            $label = trim($cells->item(0)->textContent);
            $prizeKey = $prizeMap[$label] ?? null;
            if (!$prizeKey) continue;

            // Each subsequent cell corresponds to a province
            $provinceCount = count($provinceNames);
            for ($i = 0; $i < $provinceCount; $i++) {
                $cellIndex = $i + 1;
                if ($cellIndex >= $cells->length) break;

                $cell = $cells->item($cellIndex);
                $innerHtml = '';
                foreach ($cell->childNodes as $child) {
                    $innerHtml .= $dom->saveHTML($child);
                }
                $cellText = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $innerHtml)));

                $numbers = [];
                $parts = preg_split('/[\s,;]+/', $cellText);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '' || $part === '...' || $part === '---' || $part === '____') {
                        continue;
                    }
                    if (preg_match('/^\d+$/', $part)) {
                        $numbers[] = $part;
                    }
                }

                if (!empty($numbers)) {
                    $provinceNames[$i]['prizes'][$prizeKey] = implode(',', $numbers);
                }
            }
        }

        // Check completion: all prizes for all provinces must be non-null
        $complete = true;
        foreach ($provinceNames as $prov) {
            foreach ($prov['prizes'] as $value) {
                if ($value === null) {
                    $complete = false;
                    break 2;
                }
            }
        }

        // Extract XSMT result date from the link "/xsmt/ngay-D-M-YYYY"
        $resultsDate = null;
        preg_match('/\/xsmt\/ngay-(\d{1,2})-(\d{1,2})-(\d{4})/', $html, $dateMatch);
        if ($dateMatch) {
            $resultsDate = sprintf('%s-%s-%s',
                str_pad($dateMatch[1], 2, '0', STR_PAD_LEFT),
                str_pad($dateMatch[2], 2, '0', STR_PAD_LEFT),
                $dateMatch[3]
            );
        }

        return ['provinces' => $provinceNames, 'complete' => $complete, 'results_date' => $resultsDate];
    }

    private function parseXsmnLiveHtml(string $html): array
    {
        $provinces = [];
        $complete = false;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);

        // Find the XSMN table: look for table with class tbl-xsmn, or table inside a box-ketqua with id starting with MN
        $tables = $xpath->query("//table[contains(@class, 'tbl-xsmn')]");
        if ($tables->length === 0) {
            // Fallback: find table inside div whose id starts with MN
            $tables = $xpath->query("//*[starts-with(@id, 'MN')]//table");
        }

        if ($tables->length === 0) {
            return ['provinces' => [], 'complete' => false, 'results_date' => null];
        }

        $table = $tables->item(0);

        // Extract province names from header row <th> elements
        $headerRow = $xpath->query('.//thead/tr | .//tr', $table);
        $provinceNames = [];

        if ($headerRow->length > 0) {
            $ths = $xpath->query('.//th', $headerRow->item(0));
            // Skip the first <th> (day/date label), rest are province names
            for ($i = 1; $i < $ths->length; $i++) {
                $name = trim($ths->item($i)->textContent);
                if ($name !== '') {
                    // Generate slug from province name
                    $slug = \Illuminate\Support\Str::slug($name);
                    $provinceNames[] = ['name' => $name, 'slug' => $slug];
                }
            }
        }

        if (empty($provinceNames)) {
            return ['provinces' => [], 'complete' => false, 'results_date' => null];
        }

        // Initialize prizes for each province
        foreach ($provinceNames as &$prov) {
            $prov['prizes'] = $this->emptyXsmnPrizes();
        }
        unset($prov);

        // Map row labels to prize keys
        $prizeMap = [
            'G.8' => 'prize_8',
            'G8' => 'prize_8',
            'G.7' => 'prize_7',
            'G7' => 'prize_7',
            'G.6' => 'prize_6',
            'G6' => 'prize_6',
            'G.5' => 'prize_5',
            'G5' => 'prize_5',
            'G.4' => 'prize_4',
            'G4' => 'prize_4',
            'G.3' => 'prize_3',
            'G3' => 'prize_3',
            'G.2' => 'prize_2',
            'G2' => 'prize_2',
            'G.1' => 'prize_1',
            'G1' => 'prize_1',
            'ĐB' => 'prize_special',
            'DB' => 'prize_special',
            'Đ.Biệt' => 'prize_special',
        ];

        // Parse rows
        $rows = $xpath->query('.//tr', $table);
        foreach ($rows as $row) {
            $cells = $xpath->query('.//td', $row);
            if ($cells->length < 2) continue;

            $label = trim($cells->item(0)->textContent);
            $prizeKey = $prizeMap[$label] ?? null;
            if (!$prizeKey) continue;

            // Each subsequent cell corresponds to a province
            $provinceCount = count($provinceNames);
            for ($i = 0; $i < $provinceCount; $i++) {
                $cellIndex = $i + 1;
                if ($cellIndex >= $cells->length) break;

                $cell = $cells->item($cellIndex);
                $innerHtml = '';
                foreach ($cell->childNodes as $child) {
                    $innerHtml .= $dom->saveHTML($child);
                }
                $cellText = trim(strip_tags(str_replace(['<br>', '<br/>', '<br />'], ' ', $innerHtml)));

                $numbers = [];
                $parts = preg_split('/[\s,;]+/', $cellText);
                foreach ($parts as $part) {
                    $part = trim($part);
                    if ($part === '' || $part === '...' || $part === '---' || $part === '____') {
                        continue;
                    }
                    if (preg_match('/^\d+$/', $part)) {
                        $numbers[] = $part;
                    }
                }

                if (!empty($numbers)) {
                    $provinceNames[$i]['prizes'][$prizeKey] = implode(',', $numbers);
                }
            }
        }

        // Check completion: all prizes for all provinces must be non-null
        $complete = true;
        foreach ($provinceNames as $prov) {
            foreach ($prov['prizes'] as $value) {
                if ($value === null) {
                    $complete = false;
                    break 2;
                }
            }
        }

        // Extract XSMN result date from the link "/xsmn/ngay-D-M-YYYY"
        $resultsDate = null;
        preg_match('/\/xsmn\/ngay-(\d{1,2})-(\d{1,2})-(\d{4})/', $html, $dateMatch);
        if ($dateMatch) {
            $resultsDate = sprintf('%s-%s-%s',
                str_pad($dateMatch[1], 2, '0', STR_PAD_LEFT),
                str_pad($dateMatch[2], 2, '0', STR_PAD_LEFT),
                $dateMatch[3]
            );
        }

        return ['provinces' => $provinceNames, 'complete' => $complete, 'results_date' => $resultsDate];
    }
}
