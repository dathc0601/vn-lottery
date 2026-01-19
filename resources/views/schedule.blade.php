@extends('layouts.app')

@section('title', 'Lịch Mở Thưởng - Lịch quay xổ số các tỉnh')

@section('page-content')
<div class="max-w-7xl mx-auto px-4 py-4">
    <!-- Breadcrumb Navigation -->
    <div class="text-sm text-gray-600 mb-4">
        <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Trang chủ</a>
        <span class="mx-2">/</span>
        <span>Lịch mở thưởng</span>
    </div>

    <!-- Main Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">
        <!-- Main Content (Left Column - 65%) -->
        <div class="flex-1 lg:w-2/3">
            <!-- Page Title -->
            <h1 class="text-xl font-bold text-gray-800 mb-4">Lịch Mở Thưởng Xổ Số</h1>

            <!-- Simple Table -->
            <div class="bg-white border border-gray-300">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-300">
                            <th class="px-4 py-3 text-left font-bold text-gray-800 border-r border-gray-300 align-top">
                                Khu vực
                            </th>
                            <th class="px-4 py-3 text-left font-bold text-gray-800 border-r border-gray-300 align-top">
                                Miền Bắc<br>
                                <span class="font-normal text-sm text-gray-600">18h15 - 18h35</span>
                            </th>
                            <th class="px-4 py-3 text-left font-bold text-gray-800 border-r border-gray-300 align-top">
                                Miền Trung<br>
                                <span class="font-normal text-sm text-gray-600">17h15 - 17h35</span>
                            </th>
                            <th class="px-4 py-3 text-left font-bold text-gray-800 align-top">
                                Miền Nam<br>
                                <span class="font-normal text-sm text-gray-600">16h15 - 16h35</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $dayNames = [
                                1 => 'Thứ 2',
                                2 => 'Thứ 3',
                                3 => 'Thứ 4',
                                4 => 'Thứ 5',
                                5 => 'Thứ 6',
                                6 => 'Thứ 7',
                                7 => 'Chủ nhật'
                            ];
                        @endphp

                        @foreach($scheduleByDay as $day => $regions)
                            <tr class="border-b border-gray-300 hover:bg-gray-50">
                                <td class="px-4 py-3 font-bold text-gray-800 border-r border-gray-300">
                                    {{ $dayNames[$day] }}
                                </td>
                                <td class="px-4 py-3 border-r border-gray-300">
                                    @if(count($regions['north']) > 0)
                                        @foreach($regions['north'] as $index => $province)
                                            <a href="{{ route('province.detail', ['region' => 'xsmb', 'slug' => $province->slug]) }}"
                                               class="text-blue-600 hover:underline text-sm">{{ $province->name }}</a>@if($index < count($regions['north']) - 1),@endif
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 border-r border-gray-300">
                                    @if(count($regions['central']) > 0)
                                        @foreach($regions['central'] as $index => $province)
                                            <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $province->slug]) }}"
                                               class="text-blue-600 hover:underline text-sm">{{ $province->name }}</a>@if($index < count($regions['central']) - 1),@endif
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    @if(count($regions['south']) > 0)
                                        @foreach($regions['south'] as $index => $province)
                                            <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $province->slug]) }}"
                                               class="text-blue-600 hover:underline text-sm">{{ $province->name }}</a>@if($index < count($regions['south']) - 1),@endif
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Additional Information Section -->
            <div class="mt-6 bg-gray-50 border border-gray-300 p-4">
                <h2 class="font-bold text-gray-800 mb-3">Thông tin lịch mở thưởng xổ số</h2>
                <div class="text-sm text-gray-700 space-y-2">
                    <p><strong>Miền Bắc (XSMB):</strong> Quay thưởng hàng ngày từ thứ 2 đến chủ nhật, thời gian từ 18h15 đến 18h35.</p>
                    <p><strong>Miền Trung (XSMT):</strong> Mỗi ngày có 2-3 tỉnh quay thưởng, thời gian từ 17h15 đến 17h35.</p>
                    <p><strong>Miền Nam (XSMN):</strong> Mỗi ngày có 3-6 tỉnh quay thưởng, thời gian từ 16h15 đến 16h35.</p>
                </div>
            </div>
        </div>

        <!-- Sidebar (Right Column - 35%) -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
            :showCalendar="false"
        />
    </div>
</div>
@endsection
