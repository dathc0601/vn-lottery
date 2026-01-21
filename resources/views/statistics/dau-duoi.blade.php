@extends('layouts.app')

@section('title', 'Thống kê đầu đuôi loto')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Đầu đuôi loto</span>
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
                        Thống kê đầu đuôi loto
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Thống kê số lần xuất hiện theo đầu số (chữ số đầu tiên) và đuôi số (chữ số cuối cùng) của các số loto.
                        </p>

                        <form method="GET" action="{{ route('statistics.head-tail') }}">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Thời gian:</label>
                                    <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @foreach([7 => '7 ngày', 15 => '15 ngày', 30 => '30 ngày', 60 => '60 ngày', 90 => '90 ngày'] as $days => $label)
                                            <option value="{{ $days }}" {{ $period == $days ? 'selected' : '' }}>{{ $label }}</option>
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

                @if($selectedProvince)
                <!-- Results -->
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- Head Statistics -->
                    <div class="bg-white rounded shadow overflow-hidden">
                        <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                            Thống kê theo đầu số
                        </div>

                        <div class="p-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-center border-b font-medium">Đầu</th>
                                        <th class="px-4 py-2 text-center border-b font-medium">Số lần</th>
                                        <th class="px-4 py-2 text-left border-b font-medium">Biểu đồ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $maxHead = max($headStats) ?: 1; @endphp
                                    @for($i = 0; $i <= 9; $i++)
                                        <tr class="hover:bg-orange-50">
                                            <td class="px-4 py-2 border-b text-center font-bold text-[#ff6600]">{{ $i }}</td>
                                            <td class="px-4 py-2 border-b text-center font-semibold">{{ $headStats[$i] }}</td>
                                            <td class="px-4 py-2 border-b">
                                                <div class="bg-gray-200 rounded h-4 overflow-hidden">
                                                    <div class="bg-[#ff6600] h-full rounded transition-all"
                                                        style="width: {{ ($headStats[$i] / $maxHead) * 100 }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tail Statistics -->
                    <div class="bg-white rounded shadow overflow-hidden">
                        <div class="bg-blue-500 text-white px-4 py-2 font-medium">
                            Thống kê theo đuôi số
                        </div>

                        <div class="p-4">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="px-4 py-2 text-center border-b font-medium">Đuôi</th>
                                        <th class="px-4 py-2 text-center border-b font-medium">Số lần</th>
                                        <th class="px-4 py-2 text-left border-b font-medium">Biểu đồ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $maxTail = max($tailStats) ?: 1; @endphp
                                    @for($i = 0; $i <= 9; $i++)
                                        <tr class="hover:bg-blue-50">
                                            <td class="px-4 py-2 border-b text-center font-bold text-blue-500">{{ $i }}</td>
                                            <td class="px-4 py-2 border-b text-center font-semibold">{{ $tailStats[$i] }}</td>
                                            <td class="px-4 py-2 border-b">
                                                <div class="bg-gray-200 rounded h-4 overflow-hidden">
                                                    <div class="bg-blue-500 h-full rounded transition-all"
                                                        style="width: {{ ($tailStats[$i] / $maxTail) * 100 }}%"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-white rounded shadow p-4">
                    <div class="grid md:grid-cols-3 gap-4 text-center">
                        <div class="bg-gray-50 rounded p-3">
                            <div class="text-sm text-gray-600">Tỉnh thống kê</div>
                            <div class="font-bold text-lg">{{ $selectedProvince->name }}</div>
                        </div>
                        <div class="bg-gray-50 rounded p-3">
                            <div class="text-sm text-gray-600">Số kỳ quay</div>
                            <div class="font-bold text-lg text-[#ff6600]">{{ $totalDraws }}</div>
                        </div>
                        <div class="bg-gray-50 rounded p-3">
                            <div class="text-sm text-gray-600">Thời gian</div>
                            <div class="font-bold text-lg">{{ $period }} ngày</div>
                        </div>
                    </div>
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
