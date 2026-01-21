@extends('layouts.app')

@section('title', 'Sổ Kết Quả - Xem lại kết quả xổ số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
<span class="text-gray-800 font-medium">Sổ kết quả</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content (65%) -->
        <div class="flex-1 lg:w-[100% - 275px]">
            <!-- Filter Section -->
            <div class="mb-6">
                <!-- Orange Header -->
                <div class="bg-[#ff6600] text-white px-4 py-3 font-semibold text-lg">
                    Sổ kết quả
                </div>

                <!-- Filter Form -->
                <div class="border border-[#ff6600] border-t-0 bg-white p-4">
                    <form method="GET" action="{{ route('results.book') }}" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <!-- Date From -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Ngày bắt đầu</label>
                                <input type="date" name="date_from" value="{{ $dateFrom ?? $startDate->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#ff6600] focus:border-transparent text-sm">
                            </div>
                            <!-- Date To -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Ngày kết thúc</label>
                                <input type="date" name="date_to" value="{{ $dateTo ?? $endDate->format('Y-m-d') }}"
                                       max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#ff6600] focus:border-transparent text-sm">
                            </div>
                            <!-- Province -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Chọn tỉnh</label>
                                <select name="province_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-[#ff6600] focus:border-transparent text-sm">
                                    <option value="">Tất cả tỉnh</option>
                                    @foreach($provinces->groupBy('region') as $regionName => $regionProvinces)
                                        <optgroup label="{{ $regionName == 'north' ? 'Miền Bắc' : ($regionName == 'central' ? 'Miền Trung' : 'Miền Nam') }}">
                                            @foreach($regionProvinces as $province)
                                                <option value="{{ $province->id }}" {{ $provinceId == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Submit Button -->
                            <div>
                                <button type="submit" class="w-full bg-[#ff6600] text-white px-4 py-2 rounded hover:bg-[#e55c00] transition-colors font-semibold">
                                    Xem kết quả
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Period Buttons (outside orange border) -->
                <div class="flex flex-wrap justify-center gap-2 py-3 bg-white border border-t-0 border-gray-200">
                    @foreach(['30' => '30 ngày', '60' => '60 ngày', '90' => '90 ngày', '100' => '100 ngày', '200' => '200 ngày', '300' => '300 ngày', '500' => '500 ngày'] as $days => $label)
                        <a href="{{ route('results.book', ['period' => $days, 'province_id' => $provinceId]) }}"
                           class="px-3 py-1.5 text-sm border transition-all duration-200
                                  {{ $period == $days ? 'bg-[#ff6600] text-white border-[#ff6600]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#ff6600] hover:text-[#ff6600]' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Results Display -->
            @if($results->count() > 0)
                <div class="space-y-4">
                    <!-- Full-width Result Cards -->
                    <div class="space-y-6">
                        @foreach($results as $result)
                            @php
                                $regionCode = match($result->province->region) {
                                    'north' => 'xsmb',
                                    'central' => 'xsmt',
                                    'south' => 'xsmn',
                                    default => 'xsmb'
                                };
                            @endphp
                            <x-result-card-xskt :result="$result" :region="$regionCode" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $results->links() }}
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-lg font-medium text-yellow-800">Không tìm thấy kết quả</h3>
                            <p class="text-yellow-700 mt-1">
                                Không có kết quả nào trong khoảng thời gian đã chọn. Vui lòng thử lại với bộ lọc khác.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar (35%) -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
        />

    </div>
</div>
@endsection
