@extends('layouts.app')

@section('title', 'Thống kê chu kỳ dài nhất - Phân tích khoảng cách không về')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Chu kỳ dài nhất</span>
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
                        Thống kê chu kỳ dài nhất
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Tìm chu kỳ dài nhất (khoảng thời gian lâu nhất) mà mỗi số không xuất hiện trong kết quả loto. Giúp xác định ngưỡng cực đại của từng số.
                        </p>

                        <form method="GET" action="{{ route('statistics.longest-cycle') }}">
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
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

                                <div class="md:col-span-2 lg:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Các số cần phân tích (cách nhau bởi dấu phẩy):</label>
                                    <input type="text" name="numbers" value="{{ $numbersInput }}"
                                        placeholder="VD: 01, 02, 03, 15, 27, 88"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>
                            </div>

                            <div class="mb-4 text-sm text-gray-500">
                                <strong>Ghi chú:</strong> Phân tích 2 số cuối của tất cả các giải (loto). Hệ thống sẽ tìm khoảng thời gian dài nhất mà mỗi số không xuất hiện.
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Phân tích
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && count($longestCycleData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả phân tích - {{ $selectedProvince->name }}
                    </div>

                    <div class="p-4">
                        <!-- Selected Numbers -->
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-700">Các số đang phân tích:</span>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($parsedNumbers as $num)
                                    <span class="inline-block px-3 py-1 text-sm font-bold text-white bg-[#ff6600] rounded">
                                        {{ $num }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="border border-gray-300 px-3 py-2 text-left text-sm font-medium text-gray-700">Bộ số</th>
                                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700">Chu kỳ dài nhất không về</th>
                                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-medium text-gray-700">Khoảng thời gian của chu kỳ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($longestCycleData as $data)
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-3 py-2">
                                            <span class="inline-block px-3 py-1 text-sm font-bold text-white rounded
                                                @if($data['longest_gap'] >= 15) bg-red-500
                                                @elseif($data['longest_gap'] >= 10) bg-orange-500
                                                @else bg-[#ff6600]
                                                @endif">
                                                {{ $data['number'] }}
                                            </span>
                                        </td>
                                        <td class="border border-gray-300 px-3 py-2 text-center">
                                            <span class="font-bold text-lg
                                                @if($data['longest_gap'] >= 15) text-red-600
                                                @elseif($data['longest_gap'] >= 10) text-orange-600
                                                @else text-gray-800
                                                @endif">
                                                {{ $data['longest_gap'] }}
                                            </span>
                                            <span class="text-sm text-gray-500 ml-1">ngày</span>
                                        </td>
                                        <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-600">
                                            @if($data['gap_start_date'] && $data['gap_end_date'])
                                                {{ $data['gap_start_date'] }} - {{ $data['gap_end_date'] }}
                                            @else
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Legend -->
                        <div class="mt-4 p-3 bg-gray-50 rounded text-sm">
                            <span class="font-medium text-gray-700">Chú thích màu sắc:</span>
                            <div class="flex flex-wrap gap-4 mt-2">
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-red-500 rounded mr-2"></span>
                                    <span class="text-gray-600">Chu kỳ >= 15 ngày</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-orange-500 rounded mr-2"></span>
                                    <span class="text-gray-600">Chu kỳ 10-14 ngày</span>
                                </span>
                                <span class="flex items-center">
                                    <span class="w-4 h-4 bg-[#ff6600] rounded mr-2"></span>
                                    <span class="text-gray-600">Chu kỳ < 10 ngày</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($selectedProvince && empty($numbersInput))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Vui lòng nhập các số cần phân tích.
                </div>
                @elseif(request()->has('province_id'))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Không có dữ liệu hoặc các số nhập không hợp lệ. Vui lòng nhập số từ 00-99.
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
