@extends('layouts.app')

@section('title', 'Thống kê nhanh - Tra cứu nhanh các số 00-99')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Thống kê nhanh</span>
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
                        Thống kê nhanh
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Tra cứu nhanh các số 00-99, xem ngày gần nhất xuất hiện, tổng số lần về và số ngày vắng mặt.
                        </p>

                        <form method="GET" action="{{ route('statistics.quick') }}">
                            <div class="grid md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nhóm số:</label>
                                    <select name="number_group" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        <option value="all" {{ $numberGroup == 'all' ? 'selected' : '' }}>Tất cả (00-99)</option>
                                        @for($g = 0; $g <= 90; $g += 10)
                                            <option value="{{ $g }}" {{ $numberGroup == $g ? 'selected' : '' }}>
                                                {{ str_pad($g, 2, '0', STR_PAD_LEFT) }} - {{ str_pad($g + 9, 2, '0', STR_PAD_LEFT) }}
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lọc giải:</label>
                                    <select name="prize_filter" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        <option value="all" {{ $prizeFilter == 'all' ? 'selected' : '' }}>Tất cả giải</option>
                                        <option value="special" {{ $prizeFilter == 'special' ? 'selected' : '' }}>Chỉ giải đặc biệt</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Xem kết quả
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && count($quickData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả thống kê nhanh - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-center border-b font-medium">Số</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Ngày gần nhất</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Tổng lần về</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số ngày vắng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quickData as $item)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-4 py-2 border-b text-center">
                                            <span class="inline-block w-10 h-8 leading-8 text-center font-bold text-white rounded bg-[#ff6600]">
                                                {{ $item['number'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b text-center text-gray-600">
                                            {{ $item['last_date'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold">
                                            {{ $item['total_count'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold
                                            {{ $item['days_absent'] >= 20 ? 'text-red-600' : ($item['days_absent'] >= 10 ? 'text-orange-600' : 'text-green-600') }}">
                                            {{ $item['days_absent'] >= 999 ? '-' : $item['days_absent'] }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        <span class="text-red-600 font-medium">Đỏ</span>: Vắng >= 20 ngày
                        <span class="text-orange-600 font-medium ml-4">Cam</span>: Vắng 10-19 ngày
                        <span class="text-green-600 font-medium ml-4">Xanh</span>: Vắng < 10 ngày
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
