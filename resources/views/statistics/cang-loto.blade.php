@extends('layouts.app')

@section('title', 'Tìm càng loto - Tìm kiếm 3 số liên tiếp')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Tìm càng loto</span>
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
                        Tìm càng loto
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Tìm kiếm các bộ 3 số liên tiếp xuất hiện trong kết quả xổ số. Nhập mẫu 3 số (VD: 123, 456) và chọn vị trí tìm kiếm.
                        </p>

                        <form method="GET" action="{{ route('statistics.cang-loto') }}">
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

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Mẫu càng (3 số):</label>
                                    <input type="text" name="pattern" value="{{ $pattern }}" maxlength="3" pattern="[0-9]{3}"
                                        placeholder="VD: 123"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kiểu tìm kiếm:</label>
                                    <select name="search_type" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @foreach($searchTypes as $key => $label)
                                            <option value="{{ $key }}" {{ $searchType == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#e55a00] font-medium">
                                Tìm kiếm
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Results -->
                @if($selectedProvince && count($cangData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Kết quả tìm kiếm - {{ $selectedProvince->name }} - Mẫu "{{ $pattern }}" ({{ $searchTypes[$searchType] }})
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-center border-b font-medium w-16">STT</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Ngày</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Giải đặc biệt</th>
                                    <th class="px-4 py-2 text-center border-b font-medium">Số trùng khớp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cangData as $index => $item)
                                    <tr class="hover:bg-orange-50">
                                        <td class="px-4 py-2 border-b text-center text-gray-500">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center">
                                            <span class="text-[#0066cc] font-medium">
                                                {{ $item['date']->format('d/m/Y') }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 border-b text-center font-bold text-[#ff6600]">
                                            {{ $item['special_prize'] }}
                                        </td>
                                        <td class="px-4 py-2 border-b text-center">
                                            <div class="flex flex-wrap justify-center gap-1">
                                                @foreach($item['matches'] as $match)
                                                    <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded" title="Khớp: {{ $match['matched_part'] }}">
                                                        {{ $match['full_number'] }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        Tìm thấy <strong>{{ count($cangData) }}</strong> kỳ quay có mẫu "{{ $pattern }}" xuất hiện.
                    </div>
                </div>
                @elseif($selectedProvince && strlen($pattern) === 3)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Không tìm thấy kết quả nào với mẫu "{{ $pattern }}" trong khoảng thời gian đã chọn.
                </div>
                @elseif(request()->has('province_id') && strlen($pattern) !== 3)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Vui lòng nhập đúng 3 chữ số để tìm kiếm.
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
