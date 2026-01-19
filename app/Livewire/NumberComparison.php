<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\NumberStatistic;
use Livewire\Attributes\Reactive;

class NumberComparison extends Component
{
    #[Reactive]
    public ?int $provinceId = null;

    #[Reactive]
    public ?string $period = '30d';

    public array $compareNumbers = [];

    public function mount(?int $provinceId = null, ?string $period = '30d', array $compareNumbers = [])
    {
        $this->provinceId = $provinceId;
        $this->period = $period ?? '30d';
        $this->compareNumbers = $compareNumbers;
    }

    public function addNumber(string $number)
    {
        if (!in_array($number, $this->compareNumbers) && count($this->compareNumbers) < 4) {
            $this->compareNumbers[] = $number;
        }
    }

    public function removeNumber(string $number)
    {
        $this->compareNumbers = array_values(
            array_filter($this->compareNumbers, fn($n) => $n !== $number)
        );
    }

    public function getComparisonDataProperty()
    {
        if (!$this->provinceId || empty($this->compareNumbers)) {
            return [];
        }

        $period = $this->period ?? '30d';
        $frequencyColumn = "frequency_{$period}";

        $stats = NumberStatistic::query()
            ->where('province_id', $this->provinceId)
            ->whereIn('number', $this->compareNumbers)
            ->get()
            ->keyBy('number');

        $comparisonData = [];
        foreach ($this->compareNumbers as $number) {
            $stat = $stats->get($number);
            if ($stat) {
                $comparisonData[] = [
                    'number' => $number,
                    'frequency' => $stat->$frequencyColumn,
                    'last_appeared' => $stat->last_appeared,
                    'cycle_count' => $stat->cycle_count,
                    'status' => $this->determineStatus($stat),
                    'all_frequencies' => [
                        '30d' => $stat->frequency_30d,
                        '60d' => $stat->frequency_60d,
                        '90d' => $stat->frequency_90d,
                        '100d' => $stat->frequency_100d,
                        '200d' => $stat->frequency_200d,
                        '300d' => $stat->frequency_300d,
                        '500d' => $stat->frequency_500d,
                    ],
                ];
            }
        }

        return $comparisonData;
    }

    private function determineStatus(NumberStatistic $stat): string
    {
        $period = $this->period ?? '30d';
        $frequencyKey = "frequency_{$period}";
        $frequency = $stat->$frequencyKey;

        if ($frequency === 0) return 'never';
        if ($stat->cycle_count <= 7) return 'hot';
        if ($stat->cycle_count <= 14) return 'warm';
        if ($stat->cycle_count <= 30) return 'normal';
        if ($stat->cycle_count <= 60) return 'cool';
        return 'cold';
    }

    public function render()
    {
        return view('livewire.number-comparison', [
            'comparisonData' => $this->comparisonData,
        ]);
    }
}
