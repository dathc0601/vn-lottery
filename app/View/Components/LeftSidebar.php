<?php

namespace App\View\Components;

use App\Models\Province;
use Illuminate\View\Component;

class LeftSidebar extends Component
{
    public $northProvinces;
    public $centralProvinces;
    public $southProvinces;
    public $days;

    public function __construct()
    {
        $this->northProvinces = Province::where('region', 'north')
            ->where('is_active', true)
            ->where('show_in_left_sidebar', true)
            ->orderBy('left_sidebar_order')
            ->orderBy('sort_order')
            ->get();

        $this->centralProvinces = Province::where('region', 'central')
            ->where('is_active', true)
            ->where('show_in_left_sidebar', true)
            ->orderBy('left_sidebar_order')
            ->orderBy('sort_order')
            ->get();

        $this->southProvinces = Province::where('region', 'south')
            ->where('is_active', true)
            ->where('show_in_left_sidebar', true)
            ->orderBy('left_sidebar_order')
            ->orderBy('sort_order')
            ->get();

        $this->days = [
            ['label' => 'Thứ 2', 'value' => 1],
            ['label' => 'Thứ 3', 'value' => 2],
            ['label' => 'Thứ 4', 'value' => 3],
            ['label' => 'Thứ 5', 'value' => 4],
            ['label' => 'Thứ 6', 'value' => 5],
            ['label' => 'Thứ 7', 'value' => 6],
            ['label' => 'Chủ Nhật', 'value' => 0],
        ];
    }

    /**
     * Map database region to URL prefix
     */
    public function getRegionPrefix(string $region): string
    {
        return match ($region) {
            'north' => 'xsmb',
            'central' => 'xsmt',
            'south' => 'xsmn',
            default => 'xsmb',
        };
    }

    public function render()
    {
        return view('components.left-sidebar');
    }
}
