<?php

namespace App\Filament\Widgets;

use App\Models\NumberStatistic;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\Reactive;

class NumberStatsOverview extends BaseWidget
{
    #[Reactive]
    public ?int $provinceId = null;

    #[Reactive]
    public ?string $period = '30d';

    protected function getStats(): array
    {
        if (!$this->provinceId) {
            return [
                Stat::make('No Province Selected', '—')
                    ->description('Please select a province to view statistics')
                    ->color('gray'),
            ];
        }

        $period = $this->period ?? '30d';
        $frequencyColumn = "frequency_{$period}";

        $stats = NumberStatistic::query()
            ->where('province_id', $this->provinceId)
            ->get();

        if ($stats->isEmpty()) {
            return [
                Stat::make('No Data', '—')
                    ->description('No statistics available for this province')
                    ->color('gray'),
            ];
        }

        // Calculate metrics
        $hottestStat = $stats->sortByDesc($frequencyColumn)->first();
        $coldestStat = $stats->sortBy($frequencyColumn)->first();
        $averageFrequency = $stats->avg($frequencyColumn);

        // Count trending up numbers
        $trendingUp = $stats->filter(function ($stat) {
            $recent = ($stat->frequency_30d + $stat->frequency_60d + $stat->frequency_90d) / 3;
            $older = ($stat->frequency_100d + $stat->frequency_200d + $stat->frequency_300d) / 3;
            return $recent > $older;
        });

        $statsArray = [];

        // Hottest Number
        if ($hottestStat) {
            $hottestSparkline = [
                $hottestStat->frequency_30d,
                $hottestStat->frequency_60d,
                $hottestStat->frequency_90d,
                $hottestStat->frequency_100d,
                $hottestStat->frequency_200d,
                $hottestStat->frequency_300d,
                $hottestStat->frequency_500d,
            ];

            $statsArray[] = Stat::make('🔥 Hottest Number', $hottestStat->number)
                ->description($hottestStat->$frequencyColumn . ' times | Last: ' . ($hottestStat->last_appeared ? $hottestStat->last_appeared->diffForHumans() : 'Never'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('danger')
                ->chart($hottestSparkline);
        }

        // Coldest Number
        if ($coldestStat) {
            $coldestSparkline = [
                $coldestStat->frequency_500d,
                $coldestStat->frequency_300d,
                $coldestStat->frequency_200d,
                $coldestStat->frequency_100d,
                $coldestStat->frequency_90d,
                $coldestStat->frequency_60d,
                $coldestStat->frequency_30d,
            ];

            $statsArray[] = Stat::make('❄️ Coldest Number', $coldestStat->number)
                ->description($coldestStat->$frequencyColumn . ' times | Cycle: ' . $coldestStat->cycle_count . ' days')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('info')
                ->chart($coldestSparkline);
        }

        // Trending Up
        $trendingNumbers = $trendingUp->take(3)->pluck('number')->implode(', ');
        $statsArray[] = Stat::make('📈 Trending Up', $trendingUp->count() . ' numbers')
            ->description($trendingNumbers ?: 'No trending numbers')
            ->descriptionIcon('heroicon-m-sparkles')
            ->color('success');

        // Average Frequency
        $statsArray[] = Stat::make('🎯 Average Frequency', number_format($averageFrequency, 1))
            ->description('Per 100 draws in ' . $period)
            ->descriptionIcon('heroicon-m-chart-bar')
            ->color('warning');

        return $statsArray;
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
