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
        // Get filter parameters
        $period = $request->input('period', '30'); // Default to 30 days
        $provinceId = $request->input('province_id');
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

        // Calculate statistics
        $totalResults = $query->count();
        $provinceCount = $query->distinct('province_id')->count('province_id');

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
            'totalResults',
            'provinceCount'
        ));
    }
}
