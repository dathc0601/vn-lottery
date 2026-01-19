<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Quick Filter Buttons --}}
        <div class="flex flex-wrap gap-2 mb-6">
            <x-filament::button
                wire:click="filterByToday"
                color="primary"
                size="sm"
            >
                {{ __('Hôm nay') }}
            </x-filament::button>

            <x-filament::button
                wire:click="filterByYesterday"
                color="gray"
                size="sm"
            >
                {{ __('Hôm qua') }}
            </x-filament::button>

            <x-filament::button
                wire:click="filterByThisWeek"
                color="gray"
                size="sm"
            >
                {{ __('Tuần này') }}
            </x-filament::button>

            <x-filament::button
                wire:click="filterByThisMonth"
                color="gray"
                size="sm"
            >
                {{ __('Tháng này') }}
            </x-filament::button>

            <x-filament::button
                wire:click="clearFilters"
                color="gray"
                size="sm"
                outlined
            >
                {{ __('Xóa bộ lọc') }}
            </x-filament::button>
        </div>

        {{-- Grouped Results Display --}}
        @php
            $data = $this->getGroupedResults();
            $groupedResults = $data['grouped'];
            $paginationInfo = $data['paginator'];
        @endphp

        @if($groupedResults->isEmpty())
            <div class="text-center py-12">
                <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <p class="mt-4 text-lg font-semibold">{{ __('Không tìm thấy kết quả') }}</p>
                    <p class="mt-2">{{ __('Thử điều chỉnh bộ lọc hoặc tìm kiếm của bạn') }}</p>
                </div>
            </div>
        @else
            {{-- Date Grouped Cards --}}
            <div class="space-y-8">
                @foreach($groupedResults as $dateKey => $dateResults)
                    @php
                        $date = \Carbon\Carbon::parse($dateKey);
                        $dayOfWeek = \App\Helpers\LotteryHelper::getVietnameseDayOfWeek($date);
                    @endphp

                    <div class="space-y-4">
                        {{-- Date Header --}}
                        <div class="flex items-center gap-3 pb-3 border-b-2 border-gray-300 dark:border-gray-600">
                            <div class="flex items-center gap-2">
                                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                    {{ $dayOfWeek }}, {{ $date->format('d/m/Y') }}
                                </h2>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                ({{ $dateResults->count() }} {{ __('tỉnh') }})
                            </span>
                        </div>

                        {{-- Province Cards Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach($dateResults as $result)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6">
                                    {{-- Header: Province name + Region badge --}}
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                            {{ $result->province->name }}
                                        </h3>
                                        <span class="px-2 py-1 text-xs rounded-full {{ \App\Helpers\LotteryHelper::getRegionBadgeColor($result->province->region) }}">
                                            {{ \App\Helpers\LotteryHelper::getRegionLabel($result->province->region) }}
                                        </span>
                                    </div>

                                    {{-- Turn number & Status --}}
                                    <div class="flex items-center gap-2 mb-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Kỳ') }}: {{ $result->turn_num }}
                                        </span>
                                        @if($result->status == 2)
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                {{ __('lottery_result.status.completed') }}
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                {{ __('lottery_result.status.pending') }}
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Prize Results --}}
                                    <div class="space-y-2">
                                        @if($result->prize_special)
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">ĐB:</span>
                                                <span class="text-lg font-bold text-red-600 dark:text-red-400">
                                                    {{ $result->prize_special }}
                                                </span>
                                            </div>
                                        @endif

                                        @if($result->prize_1)
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">G1:</span>
                                                <span class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ $result->prize_1 }}
                                                </span>
                                            </div>
                                        @endif

                                        {{-- Collapsible section for other prizes --}}
                                        @if($result->prize_2 || $result->prize_3 || $result->prize_4 || $result->prize_5 || $result->prize_6 || $result->prize_7 || $result->prize_8)
                                            <details class="group">
                                                <summary class="cursor-pointer text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 list-none flex items-center gap-1">
                                                    <svg class="w-4 h-4 transition-transform group-open:rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                    {{ __('Xem tất cả giải') }}
                                                </summary>
                                                <div class="mt-3 space-y-1.5 pt-3 border-t border-gray-200 dark:border-gray-700">
                                                    @if($result->prize_2)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G2:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">{{ $result->prize_2 }}</span>
                                                        </div>
                                                    @endif

                                                    @if($result->prize_3)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G3:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                @foreach(\App\Helpers\LotteryHelper::formatPrizeList($result->prize_3) as $prize)
                                                                    <span class="block">{{ $prize }}</span>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($result->prize_4)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G4:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                @foreach(\App\Helpers\LotteryHelper::formatPrizeList($result->prize_4) as $prize)
                                                                    <span class="block">{{ $prize }}</span>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($result->prize_5)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G5:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                @foreach(\App\Helpers\LotteryHelper::formatPrizeList($result->prize_5) as $prize)
                                                                    <span class="block">{{ $prize }}</span>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($result->prize_6)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G6:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                @foreach(\App\Helpers\LotteryHelper::formatPrizeList($result->prize_6) as $prize)
                                                                    <span class="block">{{ $prize }}</span>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($result->prize_7)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G7:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                @foreach(\App\Helpers\LotteryHelper::formatPrizeList($result->prize_7) as $prize)
                                                                    <span class="block">{{ $prize }}</span>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif

                                                    @if($result->prize_8)
                                                        <div class="flex justify-between text-sm">
                                                            <span class="text-gray-600 dark:text-gray-400">G8:</span>
                                                            <span class="font-medium text-gray-900 dark:text-white">
                                                                @foreach(\App\Helpers\LotteryHelper::formatPrizeList($result->prize_8) as $prize)
                                                                    <span class="block">{{ $prize }}</span>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </details>
                                        @endif
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                        <a href="{{ route('filament.admin.resources.lottery-results.edit', $result) }}"
                                           class="flex-1 text-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded transition-colors">
                                            {{ __('Sửa') }}
                                        </a>
                                        <button
                                            wire:click="deleteResult({{ $result->id }})"
                                            wire:confirm="{{ __('Bạn có chắc chắn muốn xóa kết quả này?') }}"
                                            class="flex-1 text-center px-3 py-1.5 text-sm bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-700 dark:text-red-300 rounded transition-colors">
                                            {{ __('Xóa') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($paginationInfo && $paginationInfo->hasPages())
                <div class="mt-6">
                    {{ $paginationInfo->links() }}
                </div>
            @endif
        @endif
    </div>
</x-filament-panels::page>
