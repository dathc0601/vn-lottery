@extends('layouts.app')

@section('title', 'Thống kê chu kỳ gan theo tỉnh - Phân tích số lâu chưa về')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Chu kỳ gan theo tỉnh</span>
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
                        Thống kê chu kỳ gan theo tỉnh
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Phân tích chu kỳ gan (lâu chưa về) của các số theo từng tỉnh. Nhập tối đa 10 số để xem số kỳ vắng mặt và thời điểm xuất hiện gần nhất.
                        </p>

                        <form method="GET" action="{{ route('statistics.overdue-cycles-province') }}">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Các số cần tra (tối đa 10, cách nhau bởi dấu phẩy):</label>
                                    <input type="text" name="numbers" value="{{ $numbersInput }}"
                                        placeholder="VD: 01, 02, 03, 15, 27"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lọc giải:</label>
                                    <select name="prize_filter" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        <option value="all" {{ $prizeFilter == 'all' ? 'selected' : '' }}>Tất cả giải (loto)</option>
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
                @if($selectedProvince && count($overdueData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả chu kỳ gan - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-center border-b font-medium">Số</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số kỳ vắng mặt</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Lần cuối xuất hiện</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Chu kỳ (ngày)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueData as $item)
                                    @php
                                        $colorClass = '';
                                        if ($item['draws_without_appearance'] >= 50) {
                                            $colorClass = 'text-red-600 font-bold';
                                        } elseif ($item['draws_without_appearance'] >= 30) {
                                            $colorClass = 'text-orange-600 font-semibold';
                                        }
                                    @endphp
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-4 py-2 border-b text-center">
                                            <span class="inline-block w-10 h-8 leading-8 text-center font-bold text-white rounded bg-[#ff6600]">
                                                {{ $item['number'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b text-center {{ $colorClass }}">
                                            {{ $item['draws_without_appearance'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center text-gray-600">
                                            {{ $item['last_appearance'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center text-gray-600">
                                            {{ $item['period'] !== null ? $item['period'] . ' ngày' : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        <span class="text-red-600 font-bold">Đỏ đậm</span>: Vắng >= 50 kỳ
                        <span class="text-orange-600 font-semibold ml-4">Cam</span>: Vắng 30-49 kỳ
                        <span class="ml-4">Bình thường</span>: Vắng < 30 kỳ
                    </div>
                </div>
                @elseif($selectedProvince && empty($numbersInput))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Vui lòng nhập các số cần tra cứu.
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
