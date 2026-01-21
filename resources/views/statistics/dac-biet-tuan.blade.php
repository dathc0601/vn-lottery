@extends('layouts.app')

@section('title', 'Bảng đặc biệt tuần - Giải đặc biệt theo tuần')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Bảng đặc biệt tuần</span>
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
                        Bảng đặc biệt tuần
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Bảng thống kê 2 số cuối giải đặc biệt theo từng ngày trong tuần (Thứ 2 - Chủ nhật).
                        </p>

                        <form method="GET" action="{{ route('statistics.weekly-special') }}">
                            <div class="grid md:grid-cols-3 gap-4 mb-4">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Số tuần:</label>
                                    <select name="num_weeks" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        <option value="5" {{ $numWeeks == 5 ? 'selected' : '' }}>5 tuần</option>
                                        <option value="10" {{ $numWeeks == 10 ? 'selected' : '' }}>10 tuần</option>
                                        <option value="15" {{ $numWeeks == 15 ? 'selected' : '' }}>15 tuần</option>
                                        <option value="20" {{ $numWeeks == 20 ? 'selected' : '' }}>20 tuần</option>
                                    </select>
                                </div>

                                <div class="flex items-end">
                                    <button type="submit" class="w-full px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                        Xem kết quả
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && count($weeklyData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Bảng đặc biệt tuần - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-3 py-2 text-center border-b font-medium bg-gray-200">Tuần</th>
                                    <th class="px-3 py-2 text-center border-b font-medium">T2</th>
                                    <th class="px-3 py-2 text-center border-b font-medium">T3</th>
                                    <th class="px-3 py-2 text-center border-b font-medium">T4</th>
                                    <th class="px-3 py-2 text-center border-b font-medium">T5</th>
                                    <th class="px-3 py-2 text-center border-b font-medium">T6</th>
                                    <th class="px-3 py-2 text-center border-b font-medium">T7</th>
                                    <th class="px-3 py-2 text-center border-b font-medium bg-red-100">CN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($weeklyData as $week)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-3 py-2 border-b text-center bg-gray-50 font-medium text-gray-600 whitespace-nowrap">
                                            {{ $week['week_start'] }} - {{ $week['week_end'] }}
                                        </td>
                                        @for($day = 1; $day <= 7; $day++)
                                            <td class="px-3 py-2 border-b text-center {{ $day == 7 ? 'bg-red-50' : '' }}">
                                                @if($week['days'][$day])
                                                    <span class="inline-block w-10 h-8 leading-8 text-center font-bold text-white rounded bg-[#ff6600]">
                                                        {{ $week['days'][$day] }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300">-</span>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        <strong>Ghi chú:</strong> Bảng hiển thị 2 số cuối giải đặc biệt theo từng ngày trong tuần. Ô trống (-) nghĩa là không có kết quả trong ngày đó.
                    </div>
                </div>
                @elseif($selectedProvince)
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
