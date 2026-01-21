@extends('layouts.app')

@section('title', 'Thống kê theo tổng - Nhóm số theo tổng 2 chữ số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Thống kê theo tổng</span>
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
                        Thống kê theo tổng
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Nhóm các số loto theo tổng 2 chữ số (0-9). Ví dụ: Tổng 0 gồm 00, 19, 28, 37, 46, 55, 64, 73, 82, 91.
                        </p>

                        <form method="GET" action="{{ route('statistics.by-sum') }}">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lọc tổng:</label>
                                    <select name="sum_filter" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        <option value="all" {{ $sumFilter == 'all' ? 'selected' : '' }}>Tất cả (0-9)</option>
                                        @for($s = 0; $s <= 9; $s++)
                                            <option value="{{ $s }}" {{ $sumFilter == $s ? 'selected' : '' }}>Tổng {{ $s }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Xem kết quả
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Sum Groups Reference -->
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-gray-600 text-white px-4 py-2 font-medium">
                        Bảng tham khảo nhóm tổng
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-sm">
                            @foreach($sumGroups as $sum => $numbers)
                                <div class="p-2 bg-gray-50 rounded">
                                    <span class="font-bold text-[#ff6600]">Tổng {{ $sum }}:</span>
                                    <span class="text-gray-600">{{ implode(', ', $numbers) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && count($sumData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả thống kê theo tổng - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-center border-b font-medium">Tổng</th>
                                    <th class="px-4 py-2 text-left border-b font-medium">Các số trong nhóm</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Ngày gần nhất</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Tổng lần về</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số ngày vắng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sumData as $sum => $item)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-4 py-2 border-b text-center">
                                            <span class="inline-block w-10 h-8 leading-8 text-center font-bold text-white rounded bg-[#ff6600]">
                                                {{ $item['sum'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b text-gray-600">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($item['numbers'] as $num)
                                                    <span class="inline-block px-2 py-0.5 text-xs bg-gray-100 rounded">{{ $num }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-2 border-b text-center text-gray-600">
                                            {{ $item['last_date'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold">
                                            {{ $item['total_count'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold
                                            {{ $item['days_absent'] >= 10 ? 'text-red-600' : ($item['days_absent'] >= 5 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ $item['days_absent'] >= 999 ? '-' : $item['days_absent'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif(request()->has('province_id'))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Không có dữ liệu cho khoảng thời gian đã chọn.
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
