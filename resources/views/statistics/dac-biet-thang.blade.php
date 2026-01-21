@extends('layouts.app')

@section('title', 'Bảng đặc biệt tháng - Giải đặc biệt theo tháng')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">Thống kê</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Bảng đặc biệt tháng</span>
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
                        Bảng đặc biệt tháng
                    </div>

                    <div class="p-4">
                        <p class="text-gray-600 mb-4">
                            Bảng thống kê 2 số cuối giải đặc biệt theo ngày trong tháng (hàng: ngày 1-31, cột: tháng).
                        </p>

                        <form method="GET" action="{{ route('statistics.monthly-special') }}">
                            <div class="grid md:grid-cols-5 gap-4 mb-4">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Năm:</label>
                                    <select name="year" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @foreach($availableYears as $y)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ tháng:</label>
                                    <select name="start_month" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $startMonth == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                        @endfor
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến tháng:</label>
                                    <select name="end_month" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-[#ff6600] focus:border-[#ff6600]">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}" {{ $endMonth == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                        @endfor
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

                <!-- Results -->
                @if($selectedProvince && count($monthlyData) > 0)
                <div class="bg-white rounded shadow overflow-hidden">
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Bảng đặc biệt tháng {{ $startMonth }}-{{ $endMonth }}/{{ $year }} - {{ $selectedProvince->name }}
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-2 py-2 text-center border-b font-medium bg-gray-200 sticky left-0">Ngày</th>
                                    @for($m = $startMonth; $m <= $endMonth; $m++)
                                        <th class="px-2 py-2 text-center border-b font-medium whitespace-nowrap">T{{ $m }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for($day = 1; $day <= 31; $day++)
                                    <tr class="hover:bg-orange-50 {{ $day % 2 == 0 ? 'bg-gray-50' : '' }}">
                                        <td class="px-2 py-1 border-b text-center bg-gray-100 font-medium sticky left-0">
                                            {{ str_pad($day, 2, '0', STR_PAD_LEFT) }}
                                        </td>
                                        @for($m = $startMonth; $m <= $endMonth; $m++)
                                            <td class="px-2 py-1 border-b text-center">
                                                @if(isset($monthlyData[$day][$m]) && $monthlyData[$day][$m])
                                                    <span class="inline-block w-8 h-6 leading-6 text-center font-bold text-white rounded text-xs bg-[#ff6600]">
                                                        {{ $monthlyData[$day][$m] }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-300 text-xs">-</span>
                                                @endif
                                            </td>
                                        @endfor
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="p-4 bg-gray-50 border-t text-sm text-gray-600">
                        <strong>Ghi chú:</strong> Bảng hiển thị 2 số cuối giải đặc biệt theo ngày (hàng) và tháng (cột). Ô trống (-) nghĩa là không có kết quả hoặc ngày không tồn tại trong tháng đó.
                    </div>
                </div>
                @elseif($selectedProvince)
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4 text-yellow-800">
                    Không có dữ liệu cho khoảng thời gian đã chọn.
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
