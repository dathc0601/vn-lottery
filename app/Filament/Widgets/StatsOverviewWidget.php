<?php

namespace App\Filament\Widgets;

use App\Models\Province;
use App\Models\LotteryResult;
use App\Models\ApiLog;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $todayApiRequests = ApiLog::whereDate('created_at', today())->count();
        $avgResponseTime = ApiLog::whereDate('created_at', today())->avg('response_time_ms');

        return [
            Stat::make(__('widget.stats.total_provinces'), Province::count())
                ->description(__('widget.stats.total_provinces_desc'))
                ->descriptionIcon('heroicon-o-map-pin')
                ->color('primary'),

            Stat::make(__('widget.stats.total_results'), LotteryResult::count())
                ->description(__('widget.stats.total_results_desc'))
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success'),

            Stat::make(__('widget.stats.active_provinces'), Province::where('is_active', true)->count())
                ->description(__('widget.stats.active_provinces_desc'))
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('info'),

            Stat::make(__('widget.stats.api_requests_today'), $todayApiRequests)
                ->description(__('widget.stats.api_requests_today_desc'))
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),

            Stat::make(__('widget.stats.avg_response_time'), round($avgResponseTime, 0) . ' ms')
                ->description(__('widget.stats.avg_response_time_desc'))
                ->descriptionIcon('heroicon-o-clock')
                ->color($avgResponseTime > 2000 ? 'danger' : 'success'),
        ];
    }
}
