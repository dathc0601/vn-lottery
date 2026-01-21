<?php

namespace App\Services;

use App\Models\NavigationItem;
use App\Models\Province;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class NavigationService
{
    public const CACHE_PREFIX = 'nav_';
    public const CACHE_TTL = 3600; // 1 hour

    /**
     * Get the navigation tree
     */
    public function getNavigation(): Collection
    {
        return NavigationItem::getCachedTree();
    }

    /**
     * Get XSMB day links
     */
    public function getXsmbDays(): array
    {
        return [
            ['slug' => 'thu-2', 'name' => 'XSMB Thứ 2'],
            ['slug' => 'thu-3', 'name' => 'XSMB Thứ 3'],
            ['slug' => 'thu-4', 'name' => 'XSMB Thứ 4'],
            ['slug' => 'thu-5', 'name' => 'XSMB Thứ 5'],
            ['slug' => 'thu-6', 'name' => 'XSMB Thứ 6'],
            ['slug' => 'thu-7', 'name' => 'XSMB Thứ 7'],
            ['slug' => 'chu-nhat', 'name' => 'XSMB Chủ Nhật'],
        ];
    }

    /**
     * Get XSMT day links
     */
    public function getXsmtDays(): array
    {
        return [
            ['slug' => 'thu-2', 'name' => 'XSMT Thứ 2'],
            ['slug' => 'thu-3', 'name' => 'XSMT Thứ 3'],
            ['slug' => 'thu-4', 'name' => 'XSMT Thứ 4'],
            ['slug' => 'thu-5', 'name' => 'XSMT Thứ 5'],
            ['slug' => 'thu-6', 'name' => 'XSMT Thứ 6'],
            ['slug' => 'thu-7', 'name' => 'XSMT Thứ 7'],
            ['slug' => 'chu-nhat', 'name' => 'XSMT Chủ Nhật'],
        ];
    }

    /**
     * Get XSMN day links
     */
    public function getXsmnDays(): array
    {
        return [
            ['slug' => 'thu-2', 'name' => 'XSMN Thứ 2'],
            ['slug' => 'thu-3', 'name' => 'XSMN Thứ 3'],
            ['slug' => 'thu-4', 'name' => 'XSMN Thứ 4'],
            ['slug' => 'thu-5', 'name' => 'XSMN Thứ 5'],
            ['slug' => 'thu-6', 'name' => 'XSMN Thứ 6'],
            ['slug' => 'thu-7', 'name' => 'XSMN Thứ 7'],
            ['slug' => 'chu-nhat', 'name' => 'XSMN Chủ Nhật'],
        ];
    }

    /**
     * Get days for a specific region type
     */
    public function getDaysForType(string $type): array
    {
        return match ($type) {
            'xsmb_days' => $this->getXsmbDays(),
            'xsmt_days' => $this->getXsmtDays(),
            'xsmn_days' => $this->getXsmnDays(),
            default => [],
        };
    }

    /**
     * Get provinces grouped by draw day for a specific region
     */
    public function getProvincesByDay(string $region): array
    {
        $cacheKey = self::CACHE_PREFIX . "provinces_by_day_{$region}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($region) {
            $provinces = Province::where('region', $region)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            $byDay = [];
            foreach ($provinces as $province) {
                foreach ($province->draw_days ?? [] as $day) {
                    $byDay[$day][] = $province;
                }
            }
            ksort($byDay);

            return $byDay;
        });
    }

    /**
     * Get Vietnamese day names
     */
    public function getDayNames(): array
    {
        return [
            1 => 'Thứ 2',
            2 => 'Thứ 3',
            3 => 'Thứ 4',
            4 => 'Thứ 5',
            5 => 'Thứ 6',
            6 => 'Thứ 7',
            7 => 'Chủ Nhật',
        ];
    }

    /**
     * Clear all navigation-related caches
     */
    public function clearAllCaches(): void
    {
        NavigationItem::clearCache();
        Cache::forget(self::CACHE_PREFIX . 'provinces_by_day_central');
        Cache::forget(self::CACHE_PREFIX . 'provinces_by_day_south');
    }

    /**
     * Get route base for region
     */
    public function getRouteBaseForRegion(string $type): string
    {
        return match ($type) {
            'xsmb_days' => '/xsmb/',
            'xsmt_days' => '/xsmt/',
            'xsmn_days' => '/xsmn/',
            default => '/',
        };
    }

    /**
     * Get main route for region type
     */
    public function getMainRoute(string $type): string
    {
        return match ($type) {
            'xsmb_days' => 'xsmb',
            'xsmt_days' => 'xsmt',
            'xsmn_days' => 'xsmn',
            default => 'home',
        };
    }

    /**
     * Get active pattern for region type
     */
    public function getActivePattern(string $type): string
    {
        return match ($type) {
            'xsmb_days' => 'xsmb*',
            'xsmt_days' => 'xsmt*',
            'xsmn_days' => 'xsmn*',
            default => '',
        };
    }
}
