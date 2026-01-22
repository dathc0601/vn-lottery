@extends('layouts.app-three-column')

@section('title', 'Dò Vé Số - Tra cứu kết quả xổ số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Dò vé số</span>
@endsection

@section('left-sidebar')
    <!-- Xổ Số Miền Bắc -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Xổ Số Miền Bắc</div>
        <ul class="text-sm">
            @foreach($northProvinces as $province)
                <li class="border-b border-gray-200">
                    <a href="#" data-province-id="{{ $province->id }}"
                       class="province-link block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors flex justify-between items-center">
                        {{ $province->name }}
                        @if($province->draw_days && in_array(now()->dayOfWeek, $province->draw_days))
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Xổ số Điện Toán -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Xổ số Điện Toán</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thần Tài</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Điện toán 123</a>
            </li>
            <li>
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Điện toán 636</a>
            </li>
        </ul>
    </div>

    <!-- Xổ Số Vietlott -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Xổ Số Vietlott</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="{{ route('vietlott') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Max 3D</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('vietlott') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Max 3D Pro</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('vietlott') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Mega 6/45</a>
            </li>
            <li>
                <a href="{{ route('vietlott') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Power 6/55</a>
            </li>
        </ul>
    </div>

    <!-- Xổ Số Miền Nam -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Xổ Số Miền Nam</div>
        <ul class="text-sm">
            @foreach($southProvinces as $province)
                <li class="border-b border-gray-200">
                    <a href="#" data-province-id="{{ $province->id }}"
                       class="province-link block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors flex justify-between items-center">
                        {{ $province->name }}
                        @if($province->draw_days && in_array(now()->dayOfWeek, $province->draw_days))
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Xổ Số Miền Trung -->
    <div class="sidebar-section">
        <div class="sidebar-header">Xổ Số Miền Trung</div>
        <ul class="text-sm">
            @foreach($centralProvinces as $province)
                <li class="border-b border-gray-200">
                    <a href="#" data-province-id="{{ $province->id }}"
                       class="province-link block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors flex justify-between items-center">
                        {{ $province->name }}
                        @if($province->draw_days && in_array(now()->dayOfWeek, $province->draw_days))
                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

@section('page-content')
    <!-- Page Header -->
    <div class="bg-[#ff6600] text-white px-4 py-3 font-bold">
        Dò vé số - Tra cứu kết quả xổ số
    </div>

    <!-- Main Verification Form -->
    <div class="sidebar-section" id="verify-form">
        <div class="p-4">
            <form method="POST" action="{{ route('ticket.verify') }}" class="space-y-4">
                @csrf

                <div class="grid md:grid-cols-3 gap-4">
                    <!-- Date Selection -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Ngày quay thưởng:</label>
                        <input type="date" name="draw_date"
                               value="{{ $selectedDate ? $selectedDate->format('Y-m-d') : date('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600] @error('draw_date') border-red-500 @enderror">
                        @error('draw_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Province Selection -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Chọn tỉnh:</label>
                        <select name="province_id" id="province_id" required
                                class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600] @error('province_id') border-red-500 @enderror">
                            <option value="">-- Chọn tỉnh --</option>
                            <optgroup label="Miền Bắc">
                                @foreach($northProvinces as $province)
                                    <option value="{{ $province->id }}" {{ $selectedProvinceId == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Miền Trung">
                                @foreach($centralProvinces as $province)
                                    <option value="{{ $province->id }}" {{ $selectedProvinceId == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Miền Nam">
                                @foreach($southProvinces as $province)
                                    <option value="{{ $province->id }}" {{ $selectedProvinceId == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                        @error('province_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Ticket Number Input -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Nhập số (2-6 số cuối):</label>
                        <input type="text" name="ticket_number"
                               value="{{ $ticketNumber }}"
                               placeholder="Nhập số vé..."
                               maxlength="6"
                               pattern="[0-9]{2,6}"
                               required
                               class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600] @error('ticket_number') border-red-500 @enderror">
                        @error('ticket_number')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit"
                            class="px-8 py-2 bg-[#ff6600] text-white font-bold hover:bg-[#ff7700] transition-colors">
                        Xem kết quả
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Display -->
    @if($result !== null)
        <!-- Results Title -->
        <div class="bg-[#ff6600] text-white px-4 py-2 font-bold mt-4">
            Kết quả xổ số {{ $result->province->name }} - Ngày {{ $result->draw_date->format('d/m/Y') }}
        </div>

        @if(count($matchedPrizes) > 0)
            <!-- Winning Result -->
            <div class="sidebar-section border-2 border-green-500">
                <div class="bg-green-100 p-4 text-center">
                    <div class="text-4xl mb-2">🎉</div>
                    <h2 class="text-xl font-bold text-green-700">CHÚC MỪNG! VÉ CỦA BẠN ĐÃ TRÚNG GIẢI!</h2>
                    <p class="text-green-600 mt-1">Số <strong class="text-lg">{{ $ticketNumber }}</strong> đã trúng {{ count($matchedPrizes) }} giải</p>
                </div>

                <div class="p-4">
                    @foreach($matchedPrizes as $match)
                        <div class="bg-green-50 border border-green-300 rounded p-3 mb-2">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="inline-block bg-green-200 text-green-800 px-2 py-1 rounded text-sm font-bold">
                                        {{ $match['tier'] }}
                                    </span>
                                    <span class="ml-2 text-lg font-bold text-gray-800">{{ $match['number'] }}</span>
                                </div>
{{--                                <div class="text-right">--}}
{{--                                    <span class="text-green-700 font-bold">{{ $match['amount'] }}</span>--}}
{{--                                </div>--}}
                            </div>
                        </div>
                    @endforeach

                    <div class="bg-yellow-50 border border-yellow-300 p-3 mt-4 text-sm text-yellow-800">
                        <strong>Lưu ý:</strong> Vui lòng giữ vé gốc cẩn thận và liên hệ đại lý xổ số để nhận thưởng trong vòng 60 ngày.
                    </div>
                </div>
            </div>
        @else
            <!-- No Match Result -->
            <div class="sidebar-section border-2 border-gray-300">
                <div class="bg-gray-100 p-4 text-center">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-lg font-bold text-gray-700">Vé số không trúng giải</h2>
                    <p class="text-gray-600 mt-1">Số <strong>{{ $ticketNumber }}</strong> không khớp với bất kỳ giải nào</p>
                </div>
            </div>
        @endif

        <!-- Prize Table -->
        <div class="sidebar-section mt-4">
            <div class="sidebar-header">Bảng kết quả đầy đủ</div>
            <div class="p-2">
                <table class="result-table w-full text-sm">
                    <tbody>
                        <tr class="bg-red-50">
                            <td class="prize-label w-1/4 py-2 px-3 border">Giải ĐB</td>
                            <td class="prize-special text-xl py-2 px-3 border font-bold text-red-600">{{ $result->prize_special }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Nhất</td>
                            <td class="py-2 px-3 border font-bold text-blue-700">{{ $result->prize_1 }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Nhì</td>
                            <td class="py-2 px-3 border">{{ str_replace(',', ' - ', $result->prize_2) }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Ba</td>
                            <td class="py-2 px-3 border">{{ str_replace(',', ' - ', $result->prize_3) }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Tư</td>
                            <td class="py-2 px-3 border">{{ str_replace(',', ' - ', $result->prize_4) }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Năm</td>
                            <td class="py-2 px-3 border">{{ str_replace(',', ' - ', $result->prize_5) }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Sáu</td>
                            <td class="py-2 px-3 border">{{ str_replace(',', ' - ', $result->prize_6) }}</td>
                        </tr>
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Bảy</td>
                            <td class="py-2 px-3 border">{{ str_replace(',', ' - ', $result->prize_7) }}</td>
                        </tr>
                        @if($result->prize_8)
                        <tr>
                            <td class="prize-label py-2 px-3 border">Giải Tám</td>
                            <td class="py-2 px-3 border">{{ $result->prize_8 }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @elseif($selectedDate && $selectedProvinceId && $result === null)
        <!-- No Result Found for that date/province -->
        <div class="sidebar-section mt-4 border-2 border-yellow-400">
            <div class="p-4 text-center">
                <svg class="w-16 h-16 mx-auto mb-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 1a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm4-4a1 1 0 100 2h.01a1 1 0 100-2H13zM9 9a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zM7 8a1 1 0 000 2h.01a1 1 0 000-2H7z" clip-rule="evenodd" />
                </svg>
                <h3 class="text-lg font-bold text-yellow-800">Không mở thưởng!</h3>
                <p class="text-yellow-700 mt-1">
                    Không có kết quả xổ số cho ngày {{ $selectedDate->format('d/m/Y') }}.
                </p>
                <p class="text-sm text-gray-600 mt-2">Vui lòng kiểm tra lại ngày và tỉnh đã chọn.</p>
            </div>
        </div>
    @endif

    <!-- Usage Guide -->
    <div class="sidebar-section mt-4">
        <div class="sidebar-header">Hướng dẫn dò vé số</div>
        <div class="p-4 text-sm text-gray-700 space-y-2">
            <p>1. Chọn ngày quay thưởng trên vé số của bạn</p>
            <p>2. Chọn tỉnh/thành phố phát hành vé số</p>
            <p>3. Nhập 2-6 chữ số cuối trên vé số</p>
            <p>4. Nhấn "Xem kết quả" để kiểm tra</p>
        </div>
    </div>
@endsection

@section('right-sidebar')
    <!-- Compact Ticket Checker -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Dò Vé Số</div>
        <div class="p-3 space-y-2">
            <form method="POST" action="{{ route('ticket.verify') }}">
                @csrf
                <div class="mb-2">
                    <label class="block text-xs text-gray-700 mb-1">Chọn ngày:</label>
                    <input type="date" name="draw_date"
                           value="{{ date('Y-m-d') }}"
                           max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                           class="w-full px-2 py-1 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
                </div>
                <div class="mb-2">
                    <label class="block text-xs text-gray-700 mb-1">Chọn tỉnh:</label>
                    <select name="province_id" class="w-full px-2 py-1 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
                        <option value="">-- Chọn --</option>
                        @foreach($northProvinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                        @foreach($centralProvinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                        @foreach($southProvinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-2">
                    <label class="block text-xs text-gray-700 mb-1">Nhập số:</label>
                    <input type="text" name="ticket_number"
                           placeholder="2-6 số cuối..."
                           maxlength="6"
                           class="w-full px-2 py-1 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
                </div>
                <button type="submit" class="w-full bg-[#ff6600] hover:bg-[#ff7700] text-white px-3 py-2 font-bold text-sm transition-colors">
                    Xem kết quả
                </button>
            </form>
        </div>
    </div>

    <!-- Xổ số hôm qua -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Xổ số hôm qua</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="{{ route('xsmb') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMB hôm qua
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('xsmt') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMT hôm qua
                </a>
            </li>
            <li>
                <a href="{{ route('xsmn') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMN hôm qua
                </a>
            </li>
        </ul>
    </div>

    <!-- Thống kê loto -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Thống kê loto</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="{{ route('statistics.overdue') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Thống kê loto gan
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('statistics.head-tail') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Thống kê đầu đuôi loto
                </a>
            </li>
            <li>
                <a href="{{ route('statistics.frequency') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Thống kê tần suất loto
                </a>
            </li>
        </ul>
    </div>

    <!-- Lịch mở thưởng -->
    <div class="sidebar-section mb-3">
        <div class="sidebar-header">Lịch mở thưởng</div>
        <ul class="text-sm">
            <li>
                <a href="{{ route('schedule') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Lịch quay xổ số
                </a>
            </li>
        </ul>
    </div>

    <!-- Thống kê khác -->
    <div class="sidebar-section">
        <div class="sidebar-header">Thống kê loto khác</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Tổng hợp chu kỳ đặc biệt
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Chu kỳ max dàn cùng về
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Giải đặc biệt gan
                </a>
            </li>
            <li>
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Thống kê tổng
                </a>
            </li>
        </ul>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Province selection from sidebar
    document.querySelectorAll('.province-link').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            const provinceId = this.dataset.provinceId;
            const select = document.getElementById('province_id');
            if (select && provinceId) {
                select.value = provinceId;
                // Scroll to form
                document.getElementById('verify-form').scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
</script>
@endsection
