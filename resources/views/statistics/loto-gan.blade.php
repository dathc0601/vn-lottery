@extends('layouts.app')

@section('title', 'Thống kê loto gan - Số lâu chưa về')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Loto gan</span>
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
                        Thống kê loto gan
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Thống kê các số loto lâu chưa về (số gan). Số gan càng lớn nghĩa là số đó càng lâu chưa xuất hiện.
                        </p>

                        <form method="GET" action="{{ route('statistics.overdue') }}">
                            <div class="grid md:grid-cols-4 gap-4 mb-4">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gan từ (kỳ):</label>
                                    <input type="number" name="min_gap" value="{{ $minGap }}" min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Gan đến (kỳ):</label>
                                    <input type="number" name="max_gap" value="{{ $maxGap }}" min="0"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
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
                @if($selectedProvince && count($overdueData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả thống kê loto gan - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left border-b font-medium">STT</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số kỳ gan</th>
                                    <th class="px-4 py-2 text-left border-b font-medium">Lần cuối xuất hiện</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overdueData as $index => $item)
                                    <tr class="hover:bg-orange-50 {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        <td class="px-4 py-2 border-b text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-2 border-b text-center">
                                            <span class="inline-block w-10 h-8 leading-8 text-center font-bold text-white rounded
                                                {{ $item['gap'] >= 50 ? 'bg-red-500' : ($item['gap'] >= 30 ? 'bg-orange-500' : 'bg-[#ff6600]') }}">
                                                {{ $item['number'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-semibold
                                            {{ $item['gap'] >= 50 ? 'text-red-600' : ($item['gap'] >= 30 ? 'text-orange-600' : 'text-gray-700') }}">
                                            {{ $item['gap'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-gray-600">{{ $item['last_date'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        <span class="inline-block w-4 h-4 bg-red-500 rounded mr-1"></span> Gan >= 50 kỳ
                        <span class="inline-block w-4 h-4 bg-orange-500 rounded ml-4 mr-1"></span> Gan 30-49 kỳ
                        <span class="inline-block w-4 h-4 bg-[#ff6600] rounded ml-4 mr-1"></span> Gan < 30 kỳ
                    </div>
                </div>
                @elseif($selectedProvince)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Không có số nào trong biên độ gan đã chọn.
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
