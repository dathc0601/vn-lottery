<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NumberStatistic;
use Livewire\Attributes\Reactive;

class NumberHeatMap extends Component
{
    #[Reactive]
    public ?int $provinceId = null;

    #[Reactive]
    public ?string $period = '30d';

    public ?string $selectedNumber = null;

    public function mount(?int $provinceId = null, ?string $period = '30d')
    {
        $this->provinceId = $provinceId;
        $this->period = $period ?? '30d';
    }

    public function selectNumber(string $number)
    {
        $this->selectedNumber = $number;
        $this->dispatch('number-selected', number: $number);
    }

    public function getHeatMapDataProperty()
    {
        if (!$this->provinceId) {
            return [];
        }

        $period = $this->period ?? '30d';
        $frequencyColumn = "frequency_{$period}";

        $stats = NumberStatistic::query()
            ->where('province_id', $this->provinceId)
            ->get()
            ->keyBy('number');

        $maxFrequency = $stats->max($frequencyColumn) ?: 1;

        $heatMap = [];
        for ($i = 0; $i <= 99; $i++) {
            $number = str_pad($i, 2, '0', STR_PAD_LEFT);
            $stat = $stats->get($number);
            $frequency = $stat ? $stat->$frequencyColumn : 0;

            $heatMap[] = [
                'number' => $number,
                'frequency' => $frequency,
                'color' => $this->getHeatMapColor($frequency, $maxFrequency),
                'last_appeared' => $stat?->last_appeared,
                'cycle_count' => $stat?->cycle_count ?? 0,
            ];
        }

        return $heatMap;
    }

    private function getHeatMapColor(int $frequency, int $maxFrequency): string
    {
        if ($maxFrequency === 0 || $frequency === 0) {
            return 'cold';
        }

        $percentage = ($frequency / $maxFrequency) * 100;

        if ($percentage < 25) return 'cool';
        if ($percentage < 50) return 'normal';
        if ($percentage < 75) return 'warm';
        return 'hot';
    }

    public function render()
    {
        return view('livewire.number-heat-map', [
            'heatMapData' => $this->heatMapData,
        ]);
    }
}
