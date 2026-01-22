<?php

namespace App\Http\Controllers;

use App\Models\VietlottResult;
use App\Services\VietlottDataService;

class VietlottController extends Controller
{
    public function index()
    {
        $gameTypes = VietlottDataService::getGameTypes();
        $games = [];

        foreach ($gameTypes as $gameType) {
            $gameInfo = VietlottDataService::getGameInfo($gameType);
            $latestResult = VietlottResult::where('game_type', $gameType)
                ->orderBy('draw_date', 'desc')
                ->first();

            $results = VietlottResult::where('game_type', $gameType)
                ->orderBy('draw_date', 'desc')
                ->take(10)
                ->get();

            $games[$gameType] = [
                'code' => $gameType,
                'name' => $gameInfo['name'],
                'description' => $gameInfo['description'],
                'schedule' => $gameInfo['schedule'],
                'draw_time' => $gameInfo['draw_time'],
                'ticket_price' => $gameInfo['ticket_price'],
                'latest' => $latestResult,
                'results' => $results,
                'has_data' => $results->count() > 0,
            ];
        }

        return view('vietlott', compact('games'));
    }
}
