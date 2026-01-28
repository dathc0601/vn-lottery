<?php

namespace App\Services;

use App\Models\FooterColumn;
use App\Models\Province;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FooterService
{
    protected SiteSettingsService $settings;

    public function __construct(SiteSettingsService $settings)
    {
        $this->settings = $settings;
    }

    public function getColumns(): Collection
    {
        return FooterColumn::getCachedColumns();
    }

    public function getInfoTableRows(): array
    {
        return $this->settings->getJson('footer', 'info_table_rows', []);
    }

    public function getNotes(): array
    {
        return $this->settings->getJson('footer', 'notes', []);
    }

    public function getReferenceLinks(): array
    {
        return $this->settings->getJson('footer', 'reference_links', []);
    }

    public function getScheduleData(): array
    {
        return Cache::remember('footer_schedule_data', 3600, function () {
            $regions = ['central', 'south', 'north'];
            $dayNames = [1 => 'T2', 2 => 'T3', 3 => 'T4', 4 => 'T5', 5 => 'T6', 6 => 'T7', 7 => 'CN'];
            $regionLabels = [
                'central' => 'XSMT',
                'south' => 'XSMN',
                'north' => 'XSMB',
            ];

            $schedule = [];

            foreach ($regions as $region) {
                $provinces = Province::where('region', $region)
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->get();

                $row = [
                    'label' => $regionLabels[$region] ?? strtoupper($region),
                    'region' => $region,
                    'days' => [],
                ];

                foreach ($dayNames as $dayNum => $dayLabel) {
                    $dayProvinces = $provinces->filter(function ($p) use ($dayNum) {
                        return in_array($dayNum, $p->draw_days ?? []);
                    })->values();

                    $row['days'][$dayNum] = $dayProvinces->map(function ($p) {
                        return [
                            'name' => $p->name,
                            'slug' => $p->slug,
                            'code' => $p->code,
                        ];
                    })->toArray();
                }

                $schedule[] = $row;
            }

            return [
                'dayNames' => $dayNames,
                'rows' => $schedule,
            ];
        });
    }

    public function clearScheduleCache(): void
    {
        Cache::forget('footer_schedule_data');
    }

    public function clearCache(): void
    {
        FooterColumn::clearCache();
        $this->clearScheduleCache();
    }
}
