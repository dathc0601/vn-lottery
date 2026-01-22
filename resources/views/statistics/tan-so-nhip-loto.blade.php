@extends('layouts.app')

@section('title', 'Thống kê tần số nhịp loto - Phân tích khoảng cách xuất hiện')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Tần số nhịp loto</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content -->
        <div class="flex-1 lg:w-[100% - 275px]">
            <div class="space-y-4">
                <!-- Header -->
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Thống kê tần số nhịp loto
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Theo dõi "nhịp" xuất hiện của một số cụ thể - khoảng cách (số ngày) giữa các lần số đó xuất hiện. Giúp nhận biết quy luật và tần suất về của từng số.
                        </p>

                        <form method="GET" action="{{ route('statistics.rhythm-frequency') }}">
                            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn tỉnh:</label>
                                    <select name="province_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        <option value="">-- Chọn tỉnh --</option>
                                        @foreach($provinces->groupBy('region') as $region => $regionProvinces)
                                            <optgroup label="{{ $region == 'north' ? 'Miền Bắc' : ($region == 'central' ? 'Miền Trung' : 'Miền Nam') }}">
                                                @foreach($regionProvinces as $province)
                                                    <option value="{{ $province->id }}" {{ ($selectedProvince && $selectedProvince->id == $province->id) ? 'selected' : '' }}>
                                                        {{ $province->name }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày:</label>
                                    <input type="date" name="start_date" value="{{ $startDate }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày:</label>
                                    <input type="date" name="end_date" value="{{ $endDate }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lọc theo ngày:</label>
                                    <select name="day_filter" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @foreach($dayOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $dayFilter == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="md:col-span-2 lg:col-span-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số cần phân tích (00-99):</label>
                                    <input type="text" name="number" value="{{ $number }}"
                                        placeholder="VD: 27"
                                        maxlength="2"
                                        class="w-full max-w-xs px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>
                            </div>

                            <div class="mb-4 text-sm text-gray-500">
                                <strong>Ghi chú:</strong> Nhập 1 số duy nhất (00-99) để phân tích nhịp xuất hiện. "Số nhịp" là khoảng cách (số ngày) kể từ lần xuất hiện trước đó.
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Phân tích
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && $parsedNumber && count($rhythmData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả phân tích số {{ $parsedNumber }} - {{ $selectedProvince->name }}
                    </div>

                    <div class="p-4">
                        <!-- Summary -->
                        <div class="mb-4 p-3 bg-gray-50 rounded">
                            <div class="flex flex-wrap gap-4 text-sm">
                                <span>
                                    <span class="text-gray-600">Số đang phân tích:</span>
                                    <span class="inline-block px-3 py-1 text-sm font-bold text-white bg-[#ff6600] rounded ml-2">{{ $parsedNumber }}</span>
                                </span>
                                <span>
                                    <span class="text-gray-600">Tổng số lần xuất hiện:</span>
                                    <span class="font-semibold text-gray-800 ml-1">{{ count($rhythmData) }}</span>
                                </span>
                                @if($dayFilter)
                                <span>
                                    <span class="text-gray-600">Lọc theo:</span>
                                    <span class="font-semibold text-gray-800 ml-1">{{ $dayOptions[$dayFilter] }}</span>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700">Ngày</th>
                                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700">Thứ</th>
                                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700">Về ở giải</th>
                                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700">Số nhịp xuất hiện</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rhythmData as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                            {{ $data['date']->format('d/m/Y') }}
                                        </td>
                                        <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                            {{ $data['day_of_week'] }}
                                        </td>
                                        <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                            <span class="@if($data['prize_tier'] == 'Giải ĐB') text-red-600 font-bold @else text-gray-700 @endif">
                                                {{ $data['prize_tier'] }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-3 py-2 text-center">
                                            @if($data['rhythm_count'] == 0)
                                                <span class="text-gray-400 text-sm">Lần đầu</span>
                                            @else
                                                <span class="font-bold text-lg
                                                    @if($data['rhythm_count'] >= 10) text-red-600
                                                    @elseif($data['rhythm_count'] >= 5) text-orange-600
                                                    @elseif($data['rhythm_count'] >= 3) text-yellow-600
                                                    @else text-green-600
                                                    @endif">
                                                    {{ $data['rhythm_count'] }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Statistics Summary -->
                        @php
                            $rhythmCounts = array_filter(array_column($rhythmData, 'rhythm_count'), fn($v) => $v > 0);
                            $avgRhythm = count($rhythmCounts) > 0 ? round(array_sum($rhythmCounts) / count($rhythmCounts), 1) : 0;
                            $maxRhythm = count($rhythmCounts) > 0 ? max($rhythmCounts) : 0;
                            $minRhythm = count($rhythmCounts) > 0 ? min($rhythmCounts) : 0;
                        @endphp

                        <div class="mt-4 grid md:grid-cols-3 gap-4">
                            <div class="p-3 bg-blue-50 rounded text-center">
                                <div class="text-sm text-blue-600">Nhịp trung bình</div>
                                <div class="text-2xl font-bold text-blue-800">{{ $avgRhythm }}</div>
                                <div class="text-xs text-blue-500">ngày/lần</div>
                            </div>
                            <div class="p-3 bg-green-50 rounded text-center">
                                <div class="text-sm text-green-600">Nhịp ngắn nhất</div>
                                <div class="text-2xl font-bold text-green-800">{{ $minRhythm }}</div>
                                <div class="text-xs text-green-500">ngày</div>
                            </div>
                            <div class="p-3 bg-red-50 rounded text-center">
                                <div class="text-sm text-red-600">Nhịp dài nhất</div>
                                <div class="text-2xl font-bold text-red-800">{{ $maxRhythm }}</div>
                                <div class="text-xs text-red-500">ngày</div>
                            </div>
                        </div>

                        <!-- Legend -->
                        <div class="mt-4 p-3 bg-gray-50 rounded text-sm">
                            <span class="font-medium text-gray-700">Chú thích màu số nhịp:</span>
                            <div class="flex flex-wrap gap-4 mt-2">
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-green-500 rounded mr-2"></span>
                                    <span class="text-gray-600">1-2 ngày (nhanh)</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-yellow-500 rounded mr-2"></span>
                                    <span class="text-gray-600">3-4 ngày</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-orange-500 rounded mr-2"></span>
                                    <span class="text-gray-600">5-9 ngày</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-red-500 rounded mr-2"></span>
                                    <span class="text-gray-600">10+ ngày (chậm)</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($selectedProvince && $parsedNumber && count($rhythmData) == 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Số {{ $parsedNumber }} không xuất hiện trong khoảng thời gian đã chọn.
                </div>
                @elseif($selectedProvince && empty($number))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Vui lòng nhập số cần phân tích (00-99).
                </div>
                @elseif(request()->has('province_id'))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Số nhập không hợp lệ. Vui lòng nhập số từ 00-99.
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
            :showCalendar="true"
            :showProvinces="true"
        />
    </div>
</div>
@endsection
