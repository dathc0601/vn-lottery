<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\LotteryResult;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResultsBookController extends Controller
{
    public function index(Request $request)
    {
        // Get Hanoi province for default
        $hanoiProvince = Province::where('slug', 'ha-noi')->first();
        $defaultProvinceId = $hanoiProvince ? $hanoiProvince->id : null;

        // Get filter parameters
        $period = $request->input('period', '10'); // Default to 10 days
        $provinceId = $request->input('province_id') ?? $defaultProvinceId;
        $region = $request->input('region');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        // Calculate date range based on period or custom dates
        if ($dateFrom && $dateTo) {
            $startDate = Carbon::parse($dateFrom);
            $endDate = Carbon::parse($dateTo);
        } else {
            $endDate = Carbon::today();
            $startDate = Carbon::today()->subDays((int)$period);
        }

        // Build query
        $query = LotteryResult::with('province')
            ->whereBetween('draw_date', [$startDate, $endDate])
            ->orderBy('draw_date', 'desc')
            ->orderBy('draw_time', 'desc');

        // Apply province filter
        if ($provinceId) {
            $query->where('province_id', $provinceId);
        }

        // Apply region filter
        if ($region) {
            $provinceIds = Province::where('region', $region)->pluck('id');
            $query->whereIn('province_id', $provinceIds);
        }

        // Paginate results
        $results = $query->paginate(20);

        // Get provinces for filter dropdown
        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        // Get provinces grouped by region for sidebar
        $northProvinces = Province::where('region', 'north')->where('is_active', true)->orderBy('name')->get();
        $centralProvinces = Province::where('region', 'central')->where('is_active', true)->orderBy('name')->get();
        $southProvinces = Province::where('region', 'south')->where('is_active', true)->orderBy('name')->get();

        return view('results-book', compact(
            'results',
            'provinces',
            'period',
            'provinceId',
            'region',
            'dateFrom',
            'dateTo',
            'startDate',
            'endDate',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }
}
