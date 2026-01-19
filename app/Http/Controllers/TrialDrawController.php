<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class TrialDrawController extends Controller
{
    public function index()
    {
        // Get all provinces for region selection
        $provinces = Province::where('is_active', true)
            ->orderBy('region')
            ->orderBy('name')
            ->get();

        return view('trial-draw', compact('provinces'));
    }
}
