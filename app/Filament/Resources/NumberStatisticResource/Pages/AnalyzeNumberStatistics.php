<?php

namespace App\Filament\Resources\NumberStatisticResource\Pages;

use App\Filament\Resources\NumberStatisticResource;
use App\Models\NumberStatistic;
use App\Models\Province;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class AnalyzeNumberStatistics extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = NumberStatisticResource::class;

    protected static string $view = 'filament.resources.number-statistic-resource.pages.analyze';

    protected static ?string $title = 'Number Statistics Analytics';

    public ?array $data = [];

    public ?int $selectedProvinceId = null;
    public string $selectedPeriod = '30d';
    public string $viewMode = 'heatmap';

    public function mount(): void
    {
        // Set default province to first active province
        $firstProvince = Province::where('is_active', true)->first();
        $this->selectedProvinceId = $firstProvince?->id;
        $this->selectedPeriod = '30d';

        $this->form->fill([
            'province_id' => $this->selectedProvinceId,
            'period' => $this->selectedPeriod,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('province_id')
                    ->label('Province')
                    ->options(Province::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedProvinceId = $state;
                    }),

                Forms\Components\ToggleButtons::make('period')
                    ->label('Time Period')
                    ->options([
                        '30d' => '30 Days',
                        '60d' => '60 Days',
                        '90d' => '90 Days',
                        '100d' => '100 Days',
                        '200d' => '200 Days',
                        '300d' => '300 Days',
                        '500d' => '500 Days',
                    ])
                    ->inline()
                    ->live()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedPeriod = $state ?? '30d';
                    }),
            ])
            ->statePath('data');
    }

    #[Computed]
    public function statistics()
    {
        if (!$this->selectedProvinceId) {
            return collect();
        }

        $frequencyColumn = "frequency_{$this->selectedPeriod}";

        return NumberStatistic::query()
            ->with('province')
            ->where('province_id', $this->selectedProvinceId)
            ->get()
            ->map(function ($stat) use ($frequencyColumn) {
                return [
                    'number' => $stat->number,
                    'province' => $stat->province->name,
                    'frequency' => $stat->$frequencyColumn,
                    'last_appeared' => $stat->last_appeared,
                    'cycle_count' => $stat->cycle_count,
                    'all_frequencies' => [
                        '30d' => $stat->frequency_30d,
                        '60d' => $stat->frequency_60d,
                        '90d' => $stat->frequency_90d,
                        '100d' => $stat->frequency_100d,
                        '200d' => $stat->frequency_200d,
                        '300d' => $stat->frequency_300d,
                        '500d' => $stat->frequency_500d,
                    ],
                    'status' => $this->determineStatus($stat),
                    'trend' => $this->calculateTrend($stat),
                ];
            })
            ->sortByDesc('frequency');
    }

    #[Computed]
    public function keyMetrics()
    {
        $stats = $this->statistics();

        if ($stats->isEmpty()) {
            return [
                'hottest' => null,
                'coldest' => null,
                'trending_up' => collect(),
                'average_frequency' => 0,
            ];
        }

        $hottestNumber = $stats->first();
        $coldestNumber = $stats->sortBy('frequency')->first();

        $trendingUp = $stats
            ->filter(fn($stat) => $stat['trend']['direction'] === 'up')
            ->sortByDesc('trend.delta')
            ->take(5);

        $averageFrequency = $stats->avg('frequency');

        return [
            'hottest' => $hottestNumber,
            'coldest' => $coldestNumber,
            'trending_up' => $trendingUp,
            'average_frequency' => round($averageFrequency, 1),
        ];
    }

    #[Computed]
    public function heatMapData()
    {
        $stats = $this->statistics()->keyBy('number');
        $maxFrequency = $stats->max('frequency') ?: 1;

        $heatMap = [];
        for ($i = 0; $i <= 99; $i++) {
            $number = str_pad($i, 2, '0', STR_PAD_LEFT);
            $stat = $stats->get($number);

            $heatMap[] = [
                'number' => $number,
                'frequency' => $stat['frequency'] ?? 0,
                'color' => $this->getHeatMapColor($stat['frequency'] ?? 0, $maxFrequency),
                'status' => $stat['status'] ?? 'never',
                'last_appeared' => $stat['last_appeared'] ?? null,
                'cycle_count' => $stat['cycle_count'] ?? 0,
            ];
        }

        return $heatMap;
    }

    private function getHeatMapColor(int $frequency, int $maxFrequency): string
    {
        if ($maxFrequency === 0) {
            return 'cold';
        }

        $percentage = ($frequency / $maxFrequency) * 100;

        if ($percentage === 0) return 'cold';
        if ($percentage < 25) return 'cool';
        if ($percentage < 50) return 'normal';
        if ($percentage < 75) return 'warm';
        return 'hot';
    }

    private function determineStatus(NumberStatistic $stat): string
    {
        $frequencyKey = "frequency_{$this->selectedPeriod}";
        $frequency = $stat->$frequencyKey;

        if ($frequency === 0) return 'never';
        if ($stat->cycle_count <= 7) return 'hot';
        if ($stat->cycle_count <= 14) return 'warm';
        if ($stat->cycle_count <= 30) return 'normal';
        if ($stat->cycle_count <= 60) return 'cool';
        return 'cold';
    }

    private function calculateTrend(NumberStatistic $stat): array
    {
        $frequencies = [
            $stat->frequency_30d,
            $stat->frequency_60d,
            $stat->frequency_90d,
            $stat->frequency_100d,
            $stat->frequency_200d,
            $stat->frequency_300d,
            $stat->frequency_500d,
        ];

        // Calculate if trending up or down
        $recent = array_slice($frequencies, 0, 3);
        $older = array_slice($frequencies, 3, 3);

        $recentAvg = count($recent) > 0 ? array_sum($recent) / count($recent) : 0;
        $olderAvg = count($older) > 0 ? array_sum($older) / count($older) : 0;

        $delta = $recentAvg - $olderAvg;
        $direction = $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'stable');

        return [
            'direction' => $direction,
            'delta' => abs(round($delta, 1)),
            'sparkline' => $frequencies,
        ];
    }

    public function getHeaderActions(): array
    {
        return [];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\NumberStatsOverview::class,
        ];
    }

    protected function getHeaderWidgetsData(): array
    {
        return [
            'provinceId' => $this->selectedProvinceId,
            'period' => $this->selectedPeriod ?? '30d',
        ];
    }
}
