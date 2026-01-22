@extends('layouts.app')

@section('title', 'Thống kê chu kỳ dàn loto - Phân tích bộ số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Chu kỳ dàn loto</span>
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
                        Thống kê chu kỳ dàn loto
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Phân tích chu kỳ xuất hiện của một bộ số (dàn loto). Tính toán khoảng cách dài nhất, số kỳ liên tiếp vắng mặt, tổng số lần về và chu kỳ trung bình.
                        </p>

                        <form method="GET" action="{{ route('statistics.dan-loto-cycles') }}">
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

                                <div class="md:col-span-2 lg:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dàn số (cách nhau bởi dấu phẩy):</label>
                                    <input type="text" name="numbers" value="{{ $numbersInput }}"
                                        placeholder="VD: 01, 02, 03, 15, 27, 88"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div class="flex items-end">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="check_all_together" value="1"
                                            {{ $checkAllTogether ? 'checked' : '' }}
                                            class="w-4 h-4 text-[#ff6600] border-gray-300 rounded focus:ring-[#ff6600]">
                                        <span class="ml-2 text-sm text-gray-700">Tất cả cùng về</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4 text-sm text-gray-500">
                                <strong>Ghi chú:</strong> Nếu chọn "Tất cả cùng về", hệ thống sẽ tính chu kỳ khi TẤT CẢ các số trong dàn đều xuất hiện trong cùng một kỳ quay. Ngược lại, sẽ tính khi BẤT KỲ số nào trong dàn xuất hiện.
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Phân tích
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && $cycleData)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả phân tích - {{ $selectedProvince->name }}
                    </div>

                    <div class="p-4">
                        <!-- Selected Numbers -->
                        <div class="mb-4">
                            <span class="text-sm font-medium text-gray-700">Dàn số đang phân tích:</span>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($parsedNumbers as $num)
                                    <span class="inline-block px-3 py-1 text-sm font-bold text-white bg-[#ff6600] rounded">
                                        {{ $num }}
                                    </span>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Chế độ: {{ $checkAllTogether ? 'Tất cả cùng về trong 1 kỳ' : 'Bất kỳ số nào về' }}
                            </p>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Max Gap -->
                            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
                                <div class="text-sm opacity-90">Khoảng cách dài nhất</div>
                                <div class="text-3xl font-bold mt-1">{{ $cycleData['max_gap'] }}</div>
                                <div class="text-xs opacity-75 mt-1">kỳ quay</div>
                            </div>

                            <!-- Current Streak -->
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                                <div class="text-sm opacity-90">Đang vắng mặt</div>
                                <div class="text-3xl font-bold mt-1">{{ $cycleData['current_streak'] }}</div>
                                <div class="text-xs opacity-75 mt-1">kỳ liên tiếp</div>
                            </div>

                            <!-- Total Appearances -->
                            <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-4 text-white">
                                <div class="text-sm opacity-90">Tổng số lần về</div>
                                <div class="text-3xl font-bold mt-1">{{ $cycleData['total_appearances'] }}</div>
                                <div class="text-xs opacity-75 mt-1">lần</div>
                            </div>

                            <!-- Average Gap -->
                            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                                <div class="text-sm opacity-90">Chu kỳ trung bình</div>
                                <div class="text-3xl font-bold mt-1">{{ $cycleData['average_gap'] }}</div>
                                <div class="text-xs opacity-75 mt-1">kỳ/lần</div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="grid md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Lần xuất hiện gần nhất:</span>
                                    <span class="font-semibold text-gray-800 ml-2">{{ $cycleData['last_appearance'] }}</span>
                                </div>
                                @if($cycleData['first_appearance'])
                                <div>
                                    <span class="text-gray-600">Lần xuất hiện đầu tiên (trong khoảng):</span>
                                    <span class="font-semibold text-gray-800 ml-2">{{ $cycleData['first_appearance'] }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @elseif($selectedProvince && empty($numbersInput))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Vui lòng nhập dàn số cần phân tích.
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
