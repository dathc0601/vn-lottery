@extends('layouts.app')

@section('title', 'Thống kê chu kỳ đặc biệt - Phân tích giải đặc biệt')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Chu kỳ đặc biệt</span>
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
                        Thống kê chu kỳ đặc biệt
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Phân tích chu kỳ xuất hiện của bộ số trong <strong>giải đặc biệt</strong>. Tính toán khoảng cách dài nhất (ngưỡng cực đại) không xuất hiện và số ngày gan hiện tại.
                        </p>

                        <form method="GET" action="{{ route('statistics.special-prize-cycle') }}">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dàn số (cách nhau bởi dấu phẩy):</label>
                                    <input type="text" name="numbers" value="{{ $numbersInput }}"
                                        placeholder="VD: 01, 02, 03, 15, 27, 88"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>
                            </div>

                            <div class="mb-4 text-sm text-gray-500">
                                <strong>Ghi chú:</strong> Chỉ phân tích 2 số cuối của giải đặc biệt. Hệ thống sẽ tính chu kỳ khi BẤT KỲ số nào trong dàn xuất hiện trong giải đặc biệt.
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
                            <span class="text-sm font-medium text-gray-700">Dàn số đang phân tích (Giải ĐB):</span>
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($parsedNumbers as $num)
                                    <span class="inline-block px-3 py-1 text-sm font-bold text-white bg-[#ff6600] rounded">
                                        {{ $num }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Stats Cards -->
                        <div class="grid md:grid-cols-2 gap-4">
                            <!-- Max Gap -->
                            <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-4 text-white">
                                <div class="text-sm opacity-90">Ngưỡng cực đại không xuất hiện</div>
                                <div class="text-3xl font-bold mt-1">{{ $cycleData['max_gap'] }}</div>
                                <div class="text-xs opacity-75 mt-1">ngày</div>
                                @if($cycleData['max_gap_start_date'] && $cycleData['max_gap_end_date'])
                                <div class="text-xs opacity-90 mt-2 border-t border-white/20 pt-2">
                                    Từ {{ $cycleData['max_gap_start_date'] }} đến {{ $cycleData['max_gap_end_date'] }}
                                </div>
                                @endif
                            </div>

                            <!-- Current Streak -->
                            <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                                <div class="text-sm opacity-90">Điểm gan đến nay là</div>
                                <div class="text-3xl font-bold mt-1">{{ $cycleData['current_streak'] }}</div>
                                <div class="text-xs opacity-75 mt-1">ngày</div>
                                <div class="text-xs opacity-90 mt-2 border-t border-white/20 pt-2">
                                    Kể từ lần về gần nhất
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <div class="grid md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Lần xuất hiện gần nhất (Giải ĐB):</span>
                                    <span class="font-semibold text-gray-800 ml-2">{{ $cycleData['last_appearance'] }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Khoảng thời gian phân tích:</span>
                                    <span class="font-semibold text-gray-800 ml-2">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Interpretation -->
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-medium text-blue-800 mb-2">Phân tích:</h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                @if($cycleData['current_streak'] > $cycleData['max_gap'])
                                    <li>• Dàn số đang vượt ngưỡng cực đại không xuất hiện!</li>
                                @elseif($cycleData['current_streak'] >= $cycleData['max_gap'] * 0.8)
                                    <li>• Dàn số đang gần đạt ngưỡng cực đại ({{ round($cycleData['current_streak'] / max($cycleData['max_gap'], 1) * 100) }}%).</li>
                                @else
                                    <li>• Dàn số đang ở mức {{ round($cycleData['current_streak'] / max($cycleData['max_gap'], 1) * 100) }}% so với ngưỡng cực đại.</li>
                                @endif
                            </ul>
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
