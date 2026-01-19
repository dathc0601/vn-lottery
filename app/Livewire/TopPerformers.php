<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NumberStatistic;
use Livewire\Attributes\Reactive;

class TopPerformers extends Component
{
    #[Reactive]
    public ?int $provinceId = null;

    #[Reactive]
    public ?string $period = '30d';

    public int $limit = 10;

    public function mount(?int $provinceId = null, ?string $period = '30d', int $limit = 10)
    {
        $this->provinceId = $provinceId;
        $this->period = $period ?? '30d';
        $this->limit = $limit;
    }

    public function getTopNumbersProperty()
    {
        if (!$this->provinceId) {
            return collect();
        }

        $period = $this->period ?? '30d';
        $frequencyColumn = "frequency_{$period}";

        return NumberStatistic::query()
            ->where('province_id', $this->provinceId)
            ->orderByDesc($frequencyColumn)
            ->limit($this->limit)
            ->get()
            ->map(function ($stat) use ($frequencyColumn) {
                return [
                    'number' => $stat->number,
                    'frequency' => $stat->$frequencyColumn,
                    'last_appeared' => $stat->last_appeared,
                    'cycle_count' => $stat->cycle_count,
                    'sparkline' => [
                        $stat->frequency_30d,
                        $stat->frequency_60d,
                        $stat->frequency_90d,
                        $stat->frequency_100d,
                        $stat->frequency_200d,
                        $stat->frequency_300d,
                        $stat->frequency_500d,
                    ],
                ];
            });
    }

    public function getBottomNumbersProperty()
    {
        if (!$this->provinceId) {
            return collect();
        }

        $period = $this->period ?? '30d';
        $frequencyColumn = "frequency_{$period}";

        return NumberStatistic::query()
            ->where('province_id', $this->provinceId)
            ->orderBy($frequencyColumn)
            ->limit($this->limit)
            ->get()
            ->map(function ($stat) use ($frequencyColumn) {
                return [
                    'number' => $stat->number,
                    'frequency' => $stat->$frequencyColumn,
                    'last_appeared' => $stat->last_appeared,
                    'cycle_count' => $stat->cycle_count,
                    'sparkline' => [
                        $stat->frequency_30d,
                        $stat->frequency_60d,
                        $stat->frequency_90d,
                        $stat->frequency_100d,
                        $stat->frequency_200d,
                        $stat->frequency_300d,
                        $stat->frequency_500d,
                    ],
                ];
            });
    }

    public function render()
    {
        return view('livewire.top-performers', [
            'topNumbers' => $this->topNumbers,
            'bottomNumbers' => $this->bottomNumbers,
        ]);
    }
}
