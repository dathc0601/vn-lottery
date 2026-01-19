<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NumberStatistic;
use Livewire\Attributes\Reactive;

class NumberTrendChart extends Component
{
    #[Reactive]
    public ?int $provinceId = null;

    public array $selectedNumbers = [];

    public function mount(?int $provinceId = null, array $selectedNumbers = [])
    {
        $this->provinceId = $provinceId;
        $this->selectedNumbers = $selectedNumbers;
    }

    public function addNumber(string $number)
    {
        if (!in_array($number, $this->selectedNumbers) && count($this->selectedNumbers) < 5) {
            $this->selectedNumbers[] = $number;
        }
    }

    public function removeNumber(string $number)
    {
        $this->selectedNumbers = array_values(
            array_filter($this->selectedNumbers, fn($n) => $n !== $number)
        );
    }

    public function getChartDataProperty()
    {
        if (!$this->provinceId || empty($this->selectedNumbers)) {
            return [];
        }

        $stats = NumberStatistic::query()
            ->where('province_id', $this->provinceId)
            ->whereIn('number', $this->selectedNumbers)
            ->get()
            ->keyBy('number');

        $chartData = [];
        foreach ($this->selectedNumbers as $number) {
            $stat = $stats->get($number);
            if ($stat) {
                $chartData[] = [
                    'number' => $number,
                    'data' => [
                        $stat->frequency_30d,
                        $stat->frequency_60d,
                        $stat->frequency_90d,
                        $stat->frequency_100d,
                        $stat->frequency_200d,
                        $stat->frequency_300d,
                        $stat->frequency_500d,
                    ],
                    'color' => $this->getNumberColor($number),
                ];
            }
        }

        return $chartData;
    }

    private function getNumberColor(string $number): string
    {
        $colors = [
            '#ef4444', // red-500
            '#f59e0b', // amber-500
            '#10b981', // emerald-500
            '#3b82f6', // blue-500
            '#8b5cf6', // violet-500
        ];

        $index = array_search($number, $this->selectedNumbers);
        return $colors[$index % count($colors)];
    }

    public function render()
    {
        return view('livewire.number-trend-chart', [
            'chartData' => $this->chartData,
        ]);
    }
}
