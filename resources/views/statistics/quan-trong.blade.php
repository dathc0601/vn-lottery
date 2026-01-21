@extends('layouts.app')

@section('title', 'Thống kê quan trọng - Các bộ số quan trọng')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Thống kê quan trọng</span>
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
                        Thống kê quan trọng
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Các bộ thống kê quan trọng giúp bạn phân tích và chọn số hiệu quả hơn.
                        </p>

                        <form method="GET" action="{{ route('statistics.important') }}">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Chọn bộ thống kê:</label>
                                    <select name="preset" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @foreach($presets as $key => $label)
                                            <option value="{{ $key }}" {{ $preset == $key ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
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

                <!-- Preset Description -->
                <div class="bg-blue-50 border border-blue-200 rounded p-4 text-blue-800">
                    <strong>Mô tả các bộ thống kê:</strong>
                    <ul class="mt-2 space-y-1 text-sm">
                        <li><strong>27 số về nhiều nhất 30 ngày:</strong> Danh sách 27 số xuất hiện nhiều nhất trong 30 ngày gần nhất.</li>
                        <li><strong>Số vắng mặt 10+ ngày:</strong> Các số không xuất hiện trong 10 ngày trở lên.</li>
                        <li><strong>10 số về ít nhất:</strong> 10 số có tần suất xuất hiện thấp nhất trong 30 ngày.</li>
                        <li><strong>Số về liên tiếp:</strong> Các số xuất hiện liên tiếp nhiều ngày (>=2 ngày liên tiếp).</li>
                    </ul>
                </div>

                <!-- Results -->
                @if($selectedProvince && count($importantData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        {{ $presetTitle }} - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-center border-b font-medium">STT</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số ngày vắng</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Ngày gần nhất</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Tổng lần về</th>
                                    @if($preset == 'consecutive')
                                    <th class="px-4 py-2 text-center border-b font-medium">Ngày liên tiếp</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($importantData as $index => $item)
                                    <tr class="hover:bg-orange-50 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-4 py-2 border-b text-center text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border-b text-center">
                                            <span class="inline-block w-10 h-8 leading-8 text-center font-bold text-white rounded
                                                {{ $item['total_count'] >= 10 ? 'bg-green-500' : ($item['total_count'] >= 5 ? 'bg-[#ff6600]' : 'bg-gray-400') }}">
                                                {{ $item['number'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold
                                            {{ $item['days_absent'] >= 20 ? 'text-red-600' : ($item['days_absent'] >= 10 ? 'text-orange-600' : 'text-gray-700') }}">
                                            {{ $item['days_absent'] >= 999 ? '-' : $item['days_absent'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center text-gray-600">
                                            {{ $item['last_date_display'] ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold">
                                            {{ $item['total_count'] }}
                                        </td>
                                        @if($preset == 'consecutive')
                                        <td class="px-4 py-2 border-b text-center font-bold text-[#ff6600]">
                                            {{ $item['consecutive_days'] }}
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        <span class="inline-block w-4 h-4 bg-green-500 rounded mr-1"></span> Về >= 10 lần
                        <span class="inline-block w-4 h-4 bg-[#ff6600] rounded ml-4 mr-1"></span> Về 5-9 lần
                        <span class="inline-block w-4 h-4 bg-gray-400 rounded ml-4 mr-1"></span> Về < 5 lần
                    </div>
                </div>
                @elseif($selectedProvince)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Không có dữ liệu phù hợp với bộ thống kê đã chọn.
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
