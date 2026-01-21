@extends('layouts.app')

@section('title', 'Thống kê tần suất loto - Tần suất xuất hiện các số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Tần suất loto</span>
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
                        Thống kê tần suất loto
                    </div>

                    <div class="p-4">
                        <form method="GET" action="{{ route('statistics.frequency') }}" id="frequencyForm">
                            <!-- Number Selection Grid -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn số cần thống kê:</label>

                                <!-- Quick Selection Buttons -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <button type="button" onclick="selectAll()" class="px-3 py-1 text-sm bg-[#ff6600] text-white rounded hover:bg-[#e55a00]">
                                        Chọn tất cả
                                    </button>
                                    <button type="button" onclick="deselectAll()" class="px-3 py-1 text-sm bg-gray-500 text-white rounded hover:bg-gray-600">
                                        Bỏ chọn tất cả
                                    </button>
                                    <button type="button" onclick="selectEven()" class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                                        Số chẵn
                                    </button>
                                    <button type="button" onclick="selectOdd()" class="px-3 py-1 text-sm bg-green-500 text-white rounded hover:bg-green-600">
                                        Số lẻ
                                    </button>
                                </div>

                                <!-- Head Selection (Đầu 0-9) -->
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @for($h = 0; $h <= 9; $h++)
                                        <button type="button" onclick="selectHead({{ $h }})" class="px-3 py-1 text-sm border border-[#ff6600] text-[#ff6600] rounded hover:bg-[#ff6600] hover:text-white">
                                            Đầu {{ $h }}
                                        </button>
                                    @endfor
                                </div>

                                <!-- Tail Selection (Đuôi 0-9) -->
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @for($t = 0; $t <= 9; $t++)
                                        <button type="button" onclick="selectTail({{ $t }})" class="px-3 py-1 text-sm border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white">
                                            Đuôi {{ $t }}
                                        </button>
                                    @endfor
                                </div>

                                <!-- Number Grid 00-99 -->
                                <div class="grid grid-cols-10 gap-1">
                                    @for($i = 0; $i < 100; $i++)
                                        @php $num = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <label class="cursor-pointer">
                                            <input type="checkbox" name="numbers[]" value="{{ $num }}" class="hidden peer number-checkbox"
                                                {{ in_array($num, $selectedNumbers ?? []) ? 'checked' : '' }}>
                                            <span class="block w-full text-center py-1 border rounded text-sm
                                                peer-checked:bg-[#ff6600] peer-checked:text-white peer-checked:border-[#ff6600]
                                                hover:bg-orange-100 transition-colors">
                                                {{ $num }}
                                            </span>
                                        </label>
                                    @endfor
                                </div>
                            </div>

                            <!-- Province & Date Selection -->
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày:</label>
                                    <input type="date" name="start_date" value="{{ $startDate }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày:</label>
                                    <input type="date" name="end_date" value="{{ $endDate }}"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Xem kết quả
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Results Table -->
                @if(count($frequencyData) > 0 && count($dates) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả thống kê tần suất
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-3 py-2 text-left border-b border-r font-medium sticky left-0 bg-gray-100">Số</th>
                                    @foreach($dates as $shortDate => $fullDate)
                                        <th class="px-2 py-2 text-center border-b font-medium whitespace-nowrap" title="{{ $fullDate }}">
                                            {{ $shortDate }}
                                        </th>
                                    @endforeach
                                    <th class="px-3 py-2 text-center border-b border-l font-medium bg-[#ff6600] text-white">Tổng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($frequencyData as $number => $dateData)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-3 py-2 border-b border-r font-bold text-[#ff6600] sticky left-0 bg-white">
                                            {{ $number }}
                                        </td>
                                        @php $total = 0; @endphp
                                        @foreach($dates as $shortDate => $fullDate)
                                            @php
                                                $count = $dateData[$shortDate] ?? 0;
                                                $total += $count;
                                            @endphp
                                            <td class="px-2 py-2 text-center border-b {{ $count > 0 ? 'bg-[#ff6600] text-white font-bold' : '' }}">
                                                {{ $count > 0 ? $count : '-' }}
                                            </td>
                                        @endforeach
                                        <td class="px-3 py-2 text-center border-b border-l font-bold bg-orange-100">
                                            {{ $total }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @elseif(request()->has('province_id'))
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Vui lòng chọn ít nhất một số để thống kê.
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

<script>
function selectAll() {
    document.querySelectorAll('.number-checkbox').forEach(cb => cb.checked = true);
}

function deselectAll() {
    document.querySelectorAll('.number-checkbox').forEach(cb => cb.checked = false);
}

function selectEven() {
    deselectAll();
    document.querySelectorAll('.number-checkbox').forEach(cb => {
        if (parseInt(cb.value) % 2 === 0) cb.checked = true;
    });
}

function selectOdd() {
    deselectAll();
    document.querySelectorAll('.number-checkbox').forEach(cb => {
        if (parseInt(cb.value) % 2 === 1) cb.checked = true;
    });
}

function selectHead(h) {
    deselectAll();
    document.querySelectorAll('.number-checkbox').forEach(cb => {
        if (Math.floor(parseInt(cb.value) / 10) === h) cb.checked = true;
    });
}

function selectTail(t) {
    deselectAll();
    document.querySelectorAll('.number-checkbox').forEach(cb => {
        if (parseInt(cb.value) % 10 === t) cb.checked = true;
    });
}
</script>
@endsection
