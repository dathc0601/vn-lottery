@extends('layouts.app')

@section('title', 'Thống Kê Xổ Số - Phân tích tần suất các số')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-xl p-6 shadow-lg">
        <h1 class="text-3xl font-bold mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
            Thống Kê Xổ Số
        </h1>
        <p class="text-green-100">Phân tích tần suất xuất hiện của các số từ 00 đến 99</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <form method="GET" action="{{ route('statistics') }}" class="space-y-4">
            <!-- Province and Period Selection -->
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn tỉnh</label>
                    <select name="province_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                        @foreach($provinces->groupBy('region') as $regionName => $regionProvinces)
                            <optgroup label="{{ $regionName == 'north' ? 'Miền Bắc' : ($regionName == 'central' ? 'Miền Trung' : 'Miền Nam') }}">
                                @foreach($regionProvinces as $province)
                                    <option value="{{ $province->id }}" {{ $selectedProvince && $selectedProvince->id == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Thời gian thống kê</label>
                    <select name="period" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                        @foreach(['30' => '30 ngày', '60' => '60 ngày', '90' => '90 ngày', '100' => '100 ngày', '200' => '200 ngày', '300' => '300 ngày', '500' => '500 ngày'] as $days => $label)
                            <option value="{{ $days }}" {{ $period == $days ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Sort Options -->
            <div class="grid md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sắp xếp theo</label>
                    <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                        <option value="frequency" {{ $sortBy == 'frequency' ? 'selected' : '' }}>Tần suất</option>
                        <option value="number" {{ $sortBy == 'number' ? 'selected' : '' }}>Số</option>
                        <option value="last_appeared" {{ $sortBy == 'last_appeared' ? 'selected' : '' }}>Lần cuối xuất hiện</option>
                        <option value="cycle" {{ $sortBy == 'cycle' ? 'selected' : '' }}>Chu kỳ</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Thứ tự</label>
                    <select name="sort_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                        <option value="desc" {{ $sortOrder == 'desc' ? 'selected' : '' }}>Giảm dần</option>
                        <option value="asc" {{ $sortOrder == 'asc' ? 'selected' : '' }}>Tăng dần</option>
                    </select>
                </div>
            </div>

            <!-- Action Button -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-[#4a7c2c] text-white rounded-lg hover:bg-[#5a8c3c] transition-colors font-semibold shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                    </svg>
                    Xem thống kê
                </button>
            </div>
        </form>
    </div>

    @if($selectedProvince)
        <!-- Statistics Info -->
        <div class="grid md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Tỉnh đang thống kê</p>
                        <p class="text-lg font-bold text-gray-800">{{ $selectedProvince->name }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Số kỳ quay</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $totalDraws }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500 shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Khoảng thời gian</p>
                        <p class="text-lg font-bold text-gray-800">{{ $period }} ngày</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Table -->
        @if(count($statistics) > 0)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Số
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Tần suất
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Tỷ lệ (%)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Lần cuối
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Chu kỳ (ngày)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-white uppercase tracking-wider">
                                    Trạng thái
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($statistics as $index => $stat)
                                <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }} hover:bg-green-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-2xl font-bold text-[#2d5016]">{{ $stat['number'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-lg font-semibold text-gray-900">{{ $stat['frequency'] }}</span>
                                            <div class="ml-3 flex-1 max-w-[100px]">
                                                <div class="bg-gray-200 rounded-full h-2">
                                                    <div class="bg-[#4a7c2c] h-2 rounded-full transition-all duration-300"
                                                         style="width: {{ min($stat['percentage'], 100) }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-700">{{ $stat['percentage'] }}%</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-600">
                                            {{ $stat['last_appeared'] ? $stat['last_appeared']->format('d/m/Y') : 'Chưa có' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-700">
                                            {{ $stat['cycle'] !== null ? $stat['cycle'] : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($stat['frequency'] == 0)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Chưa ra
                                            </span>
                                        @elseif($stat['cycle'] !== null && $stat['cycle'] <= 7)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                🔥 Hot
                                            </span>
                                        @elseif($stat['cycle'] !== null && $stat['cycle'] >= 30)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                ❄️ Cold
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                ✓ Bình thường
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Legend -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-blue-900 mb-2">Giải thích:</p>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li><strong>Hot (🔥):</strong> Số xuất hiện trong vòng 7 ngày gần đây</li>
                            <li><strong>Cold (❄️):</strong> Số không xuất hiện từ 30 ngày trở lên</li>
                            <li><strong>Bình thường (✓):</strong> Số xuất hiện trong khoảng 8-29 ngày</li>
                            <li><strong>Chu kỳ:</strong> Số ngày kể từ lần xuất hiện gần nhất</li>
                        </ul>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-yellow-800">Chưa có dữ liệu</h3>
                        <p class="text-yellow-700 mt-1">
                            Chưa có kết quả xổ số để thống kê. Vui lòng chọn tỉnh và thời gian khác.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
