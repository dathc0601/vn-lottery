<?php

namespace App\Http\Controllers;

use App\Models\LotteryResult;
use App\Models\Province;
use App\Models\VietlottResult;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Get latest results for each region
        $northResults = $this->getLatestResultsByRegion('north', 1);
        $centralResults = $this->getLatestResultsByRegion('central', 3);
        $southResults = $this->getLatestResultsByRegion('south', 6);

        // Add province data for sidebar
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

        // Get latest Vietlott results for each game type
        $vietlottResults = [
            'mega645' => VietlottResult::where('game_type', 'mega645')->orderBy('draw_date', 'desc')->first(),
            'power655' => VietlottResult::where('game_type', 'power655')->orderBy('draw_date', 'desc')->first(),
            'max3d' => VietlottResult::where('game_type', 'max3d')->orderBy('draw_date', 'desc')->first(),
            'max3dpro' => VietlottResult::where('game_type', 'max3dpro')->orderBy('draw_date', 'desc')->first(),
        ];

        return view('home', compact(
            'northResults',
            'centralResults',
            'southResults',
            'northProvinces',
            'centralProvinces',
            'southProvinces',
            'vietlottResults'
        ));
    }

    private function getLatestResultsByRegion($region, $limit = 10)
    {
        $provinces = Province::where('region', $region)
            ->where('is_active', true)
            ->pluck('id');

        return LotteryResult::whereIn('province_id', $provinces)
            ->with('province')
            ->orderBy('draw_date', 'desc')
            ->orderBy('draw_time', 'desc')
            ->limit($limit)
            ->get()
            ->groupBy('province_id')
            ->map(function ($results) {
                return $results->first(); // Get only the latest result per province
            })
            ->sortByDesc('draw_date')
            ->take($limit);
    }
}
