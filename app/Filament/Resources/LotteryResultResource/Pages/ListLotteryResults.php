<?php

namespace App\Filament\Resources\LotteryResultResource\Pages;

use App\Filament\Resources\LotteryResultResource;
use App\Models\LotteryResult;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class ListLotteryResults extends ListRecords
{
    protected static string $resource = LotteryResultResource::class;

    protected static string $view = 'filament.resources.lottery-result-resource.pages.list-lottery-results';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Build the base query with filters applied
     */
    protected function getFilteredQuery()
    {
        $query = LotteryResult::with(['province:id,name,region'])
            ->select([
                'id',
                'province_id',
                'turn_num',
                'draw_date',
                'draw_time',
                'prize_special',
                'prize_1',
                'prize_2',
                'prize_3',
                'prize_4',
                'prize_5',
                'prize_6',
                'prize_7',
                'prize_8',
                'status'
            ]);

        // Apply table filters
        $filters = $this->tableFilters ?? [];

        if (isset($filters['province_id']['value']) && $filters['province_id']['value']) {
            $query->where('province_id', $filters['province_id']['value']);
        }

        if (isset($filters['region']['value']) && $filters['region']['value']) {
            $query->whereHas('province', function($q) use ($filters) {
                $q->where('region', $filters['region']['value']);
            });
        }

        if (isset($filters['draw_date'])) {
            if (!empty($filters['draw_date']['from'])) {
                $query->whereDate('draw_date', '>=', $filters['draw_date']['from']);
            }
            if (!empty($filters['draw_date']['to'])) {
                $query->whereDate('draw_date', '<=', $filters['draw_date']['to']);
            }
        }

        // Apply search
        if ($search = $this->getTableSearch()) {
            $query->where(function($q) use ($search) {
                $q->where('turn_num', 'like', "%{$search}%")
                    ->orWhere('prize_special', 'like', "%{$search}%")
                    ->orWhere('prize_1', 'like', "%{$search}%")
                    ->orWhereHas('province', function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        return $query->orderBy('draw_date', 'desc')->orderBy('province_id');
    }

    /**
     * Get lottery results grouped by date
     */
    public function getGroupedResults(): array
    {
        $results = $this->getFilteredQuery()->paginate(100);

        // Group by date
        $grouped = collect($results->items())->groupBy(function($result) {
            return $result->draw_date->format('Y-m-d');
        });

        return [
            'grouped' => $grouped,
            'paginator' => $results,
        ];
    }

    /**
     * Quick filter: Today
     */
    public function filterByToday(): void
    {
        $this->tableFilters['draw_date'] = [
            'from' => now()->format('Y-m-d'),
            'to' => now()->format('Y-m-d'),
        ];
        $this->resetTable();
    }

    /**
     * Quick filter: Yesterday
     */
    public function filterByYesterday(): void
    {
        $yesterday = now()->subDay();
        $this->tableFilters['draw_date'] = [
            'from' => $yesterday->format('Y-m-d'),
            'to' => $yesterday->format('Y-m-d'),
        ];
        $this->resetTable();
    }

    /**
     * Quick filter: This Week
     */
    public function filterByThisWeek(): void
    {
        $this->tableFilters['draw_date'] = [
            'from' => now()->startOfWeek()->format('Y-m-d'),
            'to' => now()->endOfWeek()->format('Y-m-d'),
        ];
        $this->resetTable();
    }

    /**
     * Quick filter: This Month
     */
    public function filterByThisMonth(): void
    {
        $this->tableFilters['draw_date'] = [
            'from' => now()->startOfMonth()->format('Y-m-d'),
            'to' => now()->endOfMonth()->format('Y-m-d'),
        ];
        $this->resetTable();
    }

    /**
     * Clear all filters
     */
    public function clearFilters(): void
    {
        $this->tableFilters = [];
        $this->resetTable();
    }

    /**
     * Delete a result
     */
    public function deleteResult(int $id): void
    {
        try {
            $result = LotteryResult::findOrFail($id);
            $result->delete();

            Notification::make()
                ->title(__('Đã xóa thành công'))
                ->success()
                ->send();

            $this->resetTable();
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('Lỗi khi xóa'))
                ->danger()
                ->send();
        }
    }
}
