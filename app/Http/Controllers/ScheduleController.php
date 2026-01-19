<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        // Get all provinces grouped by region
        $northProvinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $centralProvinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $southProvinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Group provinces by draw days
        $scheduleByDay = [
            1 => ['north' => [], 'central' => [], 'south' => []], // Monday
            2 => ['north' => [], 'central' => [], 'south' => []], // Tuesday
            3 => ['north' => [], 'central' => [], 'south' => []], // Wednesday
            4 => ['north' => [], 'central' => [], 'south' => []], // Thursday
            5 => ['north' => [], 'central' => [], 'south' => []], // Friday
            6 => ['north' => [], 'central' => [], 'south' => []], // Saturday
            7 => ['north' => [], 'central' => [], 'south' => []], // Sunday
        ];

        // Populate schedule
        foreach ($northProvinces as $province) {
            if ($province->draw_days) {
                foreach ($province->draw_days as $day) {
                    $scheduleByDay[$day]['north'][] = $province;
                }
            }
        }

        foreach ($centralProvinces as $province) {
            if ($province->draw_days) {
                foreach ($province->draw_days as $day) {
                    $scheduleByDay[$day]['central'][] = $province;
                }
            }
        }

        foreach ($southProvinces as $province) {
            if ($province->draw_days) {
                foreach ($province->draw_days as $day) {
                    $scheduleByDay[$day]['south'][] = $province;
                }
            }
        }

        return view('schedule', compact(
            'scheduleByDay',
            'northProvinces',
            'centralProvinces',
            'southProvinces'
        ));
    }
}
