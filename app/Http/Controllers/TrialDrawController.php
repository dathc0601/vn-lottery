<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class TrialDrawController extends Controller
{
    /**
     * XSMT province schedule by day of week
     * 0 = Sunday, 1 = Monday, ..., 6 = Saturday
     */
    private const XSMT_SCHEDULE = [
        1 => ['Phú Yên', 'Thừa Thiên Huế'],           // Thứ 2
        2 => ['Đắk Lắk', 'Quảng Nam'],                // Thứ 3
        3 => ['Khánh Hòa', 'Đà Nẵng'],                // Thứ 4
        4 => ['Bình Định', 'Quảng Bình', 'Quảng Trị'], // Thứ 5
        5 => ['Gia Lai', 'Ninh Thuận'],               // Thứ 6
        6 => ['Đắk Nông', 'Quảng Ngãi', 'Đà Nẵng'],   // Thứ 7
        0 => ['Khánh Hòa', 'Kon Tum', 'Thừa Thiên Huế'], // Chủ nhật
    ];

    /**
     * XSMN province schedule by day of week
     * 0 = Sunday, 1 = Monday, ..., 6 = Saturday
     */
    private const XSMN_SCHEDULE = [
        1 => ['Đồng Tháp', 'Cà Mau', 'TP.HCM'],              // Thứ 2
        2 => ['Bạc Liêu', 'Vũng Tàu', 'Bến Tre'],            // Thứ 3
        3 => ['Đồng Nai', 'Cần Thơ', 'Sóc Trăng'],           // Thứ 4
        4 => ['Tây Ninh', 'An Giang', 'Bình Thuận'],         // Thứ 5
        5 => ['Bình Dương', 'Vĩnh Long', 'Trà Vinh'],        // Thứ 6
        6 => ['Bình Phước', 'Long An', 'Hồ Chí Minh', 'Hậu Giang'], // Thứ 7
        0 => ['Kiên Giang', 'Tiền Giang', 'Đà Lạt'],         // Chủ nhật
    ];

    /**
     * Get provinces grouped by region
     */
    private function getProvinceData()
    {
        $allProvinces = Province::where('is_active', true)
            ->orderBy('name')
            ->get();

        return [
            'northProvinces' => $allProvinces->where('region', 'north'),
            'centralProvinces' => $allProvinces->where('region', 'central'),
            'southProvinces' => $allProvinces->where('region', 'south'),
        ];
    }

    /**
     * Get XSMT provinces for a specific day of week
     */
    private function getXsmtProvincesByDay(int $dayOfWeek, $centralProvinces)
    {
        $dayProvinceNames = self::XSMT_SCHEDULE[$dayOfWeek] ?? [];

        return $centralProvinces->filter(function ($province) use ($dayProvinceNames) {
            return in_array($province->name, $dayProvinceNames);
        })->values();
    }

    /**
     * Get full XSMT schedule with province data
     */
    private function getXsmtScheduleData($centralProvinces)
    {
        $schedule = [];
        $dayNames = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            0 => 'Chủ nhật',
        ];

        foreach (self::XSMT_SCHEDULE as $day => $provinceNames) {
            $provinces = $centralProvinces->filter(function ($province) use ($provinceNames) {
                return in_array($province->name, $provinceNames);
            })->values();

            $schedule[] = [
                'day' => $day,
                'dayName' => $dayNames[$day],
                'provinceNames' => $provinceNames,
                'provinces' => $provinces,
            ];
        }

        // Sort by day (1-6, then 0 for Sunday)
        usort($schedule, function ($a, $b) {
            $aSort = $a['day'] === 0 ? 7 : $a['day'];
            $bSort = $b['day'] === 0 ? 7 : $b['day'];
            return $aSort - $bSort;
        });

        return $schedule;
    }

    /**
     * Get XSMN provinces for a specific day of week
     */
    private function getXsmnProvincesByDay(int $dayOfWeek, $southProvinces)
    {
        $dayProvinceNames = self::XSMN_SCHEDULE[$dayOfWeek] ?? [];

        return $southProvinces->filter(function ($province) use ($dayProvinceNames) {
            return in_array($province->name, $dayProvinceNames);
        })->values();
    }

    /**
     * Get full XSMN schedule with province data
     */
    private function getXsmnScheduleData($southProvinces)
    {
        $schedule = [];
        $dayNames = [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            0 => 'Chủ nhật',
        ];

        foreach (self::XSMN_SCHEDULE as $day => $provinceNames) {
            $provinces = $southProvinces->filter(function ($province) use ($provinceNames) {
                return in_array($province->name, $provinceNames);
            })->values();

            $schedule[] = [
                'day' => $day,
                'dayName' => $dayNames[$day],
                'provinceNames' => $provinceNames,
                'provinces' => $provinces,
            ];
        }

        // Sort by day (1-6, then 0 for Sunday)
        usort($schedule, function ($a, $b) {
            $aSort = $a['day'] === 0 ? 7 : $a['day'];
            $bSort = $b['day'] === 0 ? 7 : $b['day'];
            return $aSort - $bSort;
        });

        return $schedule;
    }

    /**
     * Main trial draw page (defaults to XSMB)
     */
    public function index()
    {
        return $this->xsmb();
    }

    /**
     * XSMB trial draw page
     */
    public function xsmb()
    {
        $provinceData = $this->getProvinceData();
        $provinces = $provinceData['northProvinces'];

        return view('trial-draw', array_merge($provinceData, [
            'activeTab' => 'xsmb',
            'provinces' => $provinces,
            'regionName' => 'Miền Bắc',
        ]));
    }

    /**
     * XSMT trial draw page
     */
    public function xsmt()
    {
        $provinceData = $this->getProvinceData();
        $provinces = $provinceData['centralProvinces'];

        // Current day of week (0 = Sunday, 1 = Monday, ...)
        $currentDayOfWeek = (int) now()->format('w');

        // Get provinces for current day
        $provincesForDay = $this->getXsmtProvincesByDay($currentDayOfWeek, $provinces);

        // Get full schedule data for SEO content
        $scheduleData = $this->getXsmtScheduleData($provinces);

        // Get raw schedule for JavaScript
        $xsmtSchedule = self::XSMT_SCHEDULE;

        return view('trial-draw', array_merge($provinceData, [
            'activeTab' => 'xsmt',
            'provinces' => $provinces,
            'regionName' => 'Miền Trung',
            'currentDayOfWeek' => $currentDayOfWeek,
            'provincesForDay' => $provincesForDay,
            'scheduleData' => $scheduleData,
            'xsmtSchedule' => $xsmtSchedule,
        ]));
    }

    /**
     * XSMN trial draw page
     */
    public function xsmn()
    {
        $provinceData = $this->getProvinceData();
        $provinces = $provinceData['southProvinces'];

        // Current day of week (0 = Sunday, 1 = Monday, ...)
        $currentDayOfWeek = (int) now()->format('w');

        // Get provinces for current day
        $provincesForDay = $this->getXsmnProvincesByDay($currentDayOfWeek, $provinces);

        // Get full schedule data for SEO content
        $scheduleData = $this->getXsmnScheduleData($provinces);

        // Get raw schedule for JavaScript
        $xsmnSchedule = self::XSMN_SCHEDULE;

        return view('trial-draw', array_merge($provinceData, [
            'activeTab' => 'xsmn',
            'provinces' => $provinces,
            'regionName' => 'Miền Nam',
            'currentDayOfWeek' => $currentDayOfWeek,
            'provincesForDay' => $provincesForDay,
            'scheduleData' => $scheduleData,
            'xsmnSchedule' => $xsmnSchedule,
        ]));
    }
}
