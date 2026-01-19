<?php

namespace App\Filament\Resources\ProvinceResource\Pages;

use App\Filament\Resources\ProvinceResource;
use App\Models\Province;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class ListProvinces extends ListRecords
{
    protected static string $resource = ProvinceResource::class;

    protected static string $view = 'filament.resources.province-resource.pages.list-provinces';

    // Livewire properties for filtering and searching
    public string $activeFilter = 'all';
    public string $search = '';
    public string $sortBy = 'sort_order';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Get filtered and sorted provinces
     */
    public function getProvincesProperty()
    {
        return Province::query()
            ->when($this->activeFilter !== 'all', function (Builder $query) {
                $query->where('region', $this->activeFilter);
            })
            ->when($this->search, function (Builder $query) {
                $query->where(function (Builder $q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->orderBy($this->sortBy === 'sort_order' ? 'sort_order' : $this->sortBy)
            ->get();
    }

    /**
     * Get total count of provinces
     */
    public function getTotalCountProperty(): int
    {
        return Province::count();
    }

    /**
     * Get count of provinces in North region
     */
    public function getNorthCountProperty(): int
    {
        return Province::where('region', 'north')->count();
    }

    /**
     * Get count of provinces in Central region
     */
    public function getCentralCountProperty(): int
    {
        return Province::where('region', 'central')->count();
    }

    /**
     * Get count of provinces in South region
     */
    public function getSouthCountProperty(): int
    {
        return Province::where('region', 'south')->count();
    }

    /**
     * Filter provinces by region
     */
    public function filterByRegion(?string $region): void
    {
        $this->activeFilter = $region ?? 'all';
    }

    /**
     * Toggle province active status
     */
    public function toggleActive(int $provinceId, bool $active): void
    {
        $province = Province::find($provinceId);

        if ($province) {
            $province->is_active = $active;
            $province->save();

            Notification::make()
                ->title($active ? 'Đã kích hoạt tỉnh' : 'Đã tạm ngưng tỉnh')
                ->body("Tỉnh {$province->name} đã được " . ($active ? 'kích hoạt' : 'tạm ngưng'))
                ->success()
                ->send();
        }
    }

    /**
     * Fetch lottery results for a province
     */
    public function fetchProvinceResults(int $provinceId): void
    {
        $province = Province::find($provinceId);

        if ($province) {
            \App\Jobs\FetchLotteryResultsJob::dispatch($province->code);

            Notification::make()
                ->title('Đang lấy dữ liệu')
                ->body("Đang lấy kết quả xổ số cho {$province->name}")
                ->success()
                ->send();
        }
    }

    /**
     * Update province display order
     */
    public function updateProvinceOrder(array $order): void
    {
        foreach ($order as $index => $provinceId) {
            Province::where('id', $provinceId)->update([
                'sort_order' => $index + 1
            ]);
        }

        Notification::make()
            ->title('Đã cập nhật thứ tự')
            ->body('Thứ tự hiển thị tỉnh thành đã được cập nhật')
            ->success()
            ->send();
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        $this->activeFilter = 'all';
        $this->search = '';
        $this->sortBy = 'sort_order';
    }
}
