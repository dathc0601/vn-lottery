<?php

namespace App\Filament\Widgets;

use App\Jobs\FetchAllProvincesJob;
use App\Jobs\GenerateStatisticsJob;
use App\Models\ApiLog;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class QuickActionsWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-actions-widget';

    protected int | string | array $columnSpan = 'full';

    public function fetchAllProvinces(): void
    {
        FetchAllProvincesJob::dispatch();

        Notification::make()
            ->title(__('widget.quick_actions.fetch_all'))
            ->body(__('widget.quick_actions.fetch_all_success'))
            ->success()
            ->send();
    }

    public function generateStatistics(): void
    {
        GenerateStatisticsJob::dispatch();

        Notification::make()
            ->title(__('widget.quick_actions.generate_stats'))
            ->body(__('widget.quick_actions.generate_stats_success'))
            ->success()
            ->send();
    }

    public function clearOldLogs(): void
    {
        $deleted = ApiLog::where('created_at', '<', now()->subDays(30))->delete();

        Notification::make()
            ->title(__('widget.quick_actions.clear_logs'))
            ->body(__('widget.quick_actions.clear_logs_success', ['count' => $deleted]))
            ->success()
            ->send();
    }
}
