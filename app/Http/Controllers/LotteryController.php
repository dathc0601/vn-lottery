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

    public function provinceDetail($region, $slug)
    {
        $province = Province::where('slug', $slug)->firstOrFail();

        $results = LotteryResult::where('province_id', $province->id)
            ->orderBy('draw_date', 'desc')
            ->paginate(20);

        return view('province-detail', compact('province', 'results'));
    }
}
