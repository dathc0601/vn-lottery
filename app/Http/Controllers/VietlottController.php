<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VietlottController extends Controller
{
    public function index()
    {
        // Games information
        $games = [
            [
                'name' => 'Mega 6/45',
                'code' => 'mega645',
                'description' => 'Chọn 6 số từ 01-45',
                'jackpot' => ''  // To be fetched from API
            ],
            [
                'name' => 'Power 6/55',
                'code' => 'power655',
                'description' => 'Chọn 6 số từ 01-55',
                'jackpot' => '' // To be fetched from API
            ],
            [
                'name' => 'Max 3D',
                'code' => 'max3d',
                'description' => 'Chọn 3 số từ 000-999',
                'jackpot' => '' // To be fetched from API
            ],
            [
                'name' => 'Max 3D Pro',
                'code' => 'max3dpro',
                'description' => 'Chọn 3 số từ 000-999 (Nâng cao)',
                'jackpot' => '' // To be fetched from API
            ],
        ];

        return view('vietlott', compact('games'));
    }
}
