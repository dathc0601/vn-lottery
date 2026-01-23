<?php

namespace App\Http\Controllers;

use App\Models\VietlottResult;
use App\Models\Province;
use App\Services\VietlottDataService;

class VietlottController extends Controller
{
    private const RESULTS_PER_PAGE = 10;

    /**
     * Redirect to default game (Mega 6/45)
     */
    public function index()
    {
        return redirect()->route('vietlott.mega645');
    }

    /**
     * Mega 6/45 page
     */
    public function mega645()
    {
        return $this->showGame('mega645');
    }

    /**
     * Power 6/55 page
     */
    public function power655()
    {
        return $this->showGame('power655');
    }

    /**
     * Max 3D page
     */
    public function max3d()
    {
        return $this->showGame('max3d');
    }

    /**
     * Max 3D Pro page
     */
    public function max3dpro()
    {
        return $this->showGame('max3dpro');
    }

    /**
     * Common method for showing game pages
     */
    private function showGame(string $gameType)
    {
        $gameInfo = VietlottDataService::getGameInfo($gameType);
        $allGamesInfo = VietlottDataService::getAllGameInfo();

        $results = VietlottResult::where('game_type', $gameType)
            ->orderBy('draw_date', 'desc')
            ->take(self::RESULTS_PER_PAGE)
            ->get();

        $totalResults = VietlottResult::where('game_type', $gameType)->count();
        $hasMoreResults = $totalResults > self::RESULTS_PER_PAGE;

        // Get provinces for sidebar
        $northProvinces = Province::where('region', 'north')->orderBy('name')->get();
        $centralProvinces = Province::where('region', 'central')->orderBy('name')->get();
        $southProvinces = Province::where('region', 'south')->orderBy('name')->get();

        $viewName = match($gameType) {
            'mega645' => 'vietlott.mega645',
            'power655' => 'vietlott.power655',
            'max3d' => 'vietlott.max3d',
            'max3dpro' => 'vietlott.max3dpro',
            default => 'vietlott.mega645',
        };

        return view($viewName, compact(
            'gameType',
            'gameInfo',
            'allGamesInfo',
            'results',
            'hasMoreResults',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }

    /**
     * AJAX endpoint for loading more results
     */
    public function loadMore(string $gameType, int $page)
    {
        $offset = $page * self::RESULTS_PER_PAGE;

        $results = VietlottResult::where('game_type', $gameType)
            ->orderBy('draw_date', 'desc')
            ->skip($offset)
            ->take(self::RESULTS_PER_PAGE)
            ->get();

        $totalResults = VietlottResult::where('game_type', $gameType)->count();
        $hasMoreResults = ($offset + self::RESULTS_PER_PAGE) < $totalResults;

        $partialView = in_array($gameType, ['mega645', 'power655'])
            ? 'vietlott.partials.mega-results'
            : 'vietlott.partials.max3d-results';

        return response()->json([
            'html' => view($partialView, compact('results', 'gameType'))->render(),
            'hasMore' => $hasMoreResults,
            'nextPage' => $page + 1,
        ]);
    }
}
