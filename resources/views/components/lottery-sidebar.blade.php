@props([
    'northProvinces' => collect([]),
    'centralProvinces' => collect([]),
    'southProvinces' => collect([]),
    'showCalendar' => true,
    'showProvinces' => true,
    'region' => 'xsmb',  // Add region prop
])

<div class="space-y-3 w-full lg:w-[250px]">
    <!-- Calendar Widget -->
    @if($showCalendar)
        <x-calendar-widget :region="$region" />
    @endif

    <!-- Ticket Checker Section -->
    <div class="sidebar-section">
        <div class="sidebar-header">Dò Vé Số</div>
        <div class="p-3 space-y-2">
            <div>
                <label class="block text-sm text-gray-700 mb-1">Chọn ngày:</label>
                <input type="date"
                       value="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Chọn tỉnh:</label>
                <select class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
                    <option>Miền Bắc</option>
                    @foreach($northProvinces as $province)
                        <option value="{{ $province->slug }}">{{ $province->name }}</option>
                    @endforeach
                    <option disabled>──────────</option>
                    <option>Miền Trung</option>
                    @foreach($centralProvinces as $province)
                        <option value="{{ $province->slug }}">{{ $province->name }}</option>
                    @endforeach
                    <option disabled>──────────</option>
                    <option>Miền Nam</option>
                    @foreach($southProvinces as $province)
                        <option value="{{ $province->slug }}">{{ $province->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Nhập số:</label>
                <input type="text"
                       placeholder="Nhập số cần dò..."
                       class="w-full px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
            </div>
            <button class="w-full bg-[#ff6600] hover:bg-[#ff7700] text-white px-4 py-2 font-bold text-sm transition-colors rounded">
                Xem kết quả
            </button>
        </div>
    </div>

    <!-- Yesterday's Results Section -->
    <div class="sidebar-section">
        <div class="sidebar-header">Xổ số hôm qua</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="{{ route('xsmb') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMB hôm qua
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('xsmt') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMT hôm qua
                </a>
            </li>
            <li>
                <a href="{{ route('xsmn') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMN hôm qua
                </a>
            </li>
        </ul>
    </div>

    @if($showProvinces)
        <!-- Province Listings by Region -->

        <!-- Xổ Số Miền Bắc -->
        <div class="sidebar-section">
            <div class="sidebar-header">Xổ Số Miền Bắc</div>
            <ul class="text-sm">
                @foreach($northProvinces as $province)
                    <li class="border-b border-gray-200">
                        <a href="{{ route('province.detail', ['region' => 'xsmb', 'slug' => $province->slug]) }}"
                           class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors flex justify-between items-center">
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
        <div class="sidebar-section">
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
        <div class="sidebar-section">
            <div class="sidebar-header">Xổ Số Vietlott</div>
            <ul class="text-sm">
                <li class="border-b border-gray-200">
                    <a href="{{ route('vietlott.mega645') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Mega 6/45</a>
                </li>
                <li class="border-b border-gray-200">
                    <a href="{{ route('vietlott.power655') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Power 6/55</a>
                </li>
                <li class="border-b border-gray-200">
                    <a href="{{ route('vietlott.max3d') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Max 3D</a>
                </li>
                <li>
                    <a href="{{ route('vietlott.max3dpro') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Max 3D Pro</a>
                </li>
            </ul>
        </div>

        <!-- Xổ Số Miền Nam -->
        <div class="sidebar-section">
            <div class="sidebar-header">Xổ Số Miền Nam</div>
            <ul class="text-sm">
                @foreach($southProvinces as $province)
                    <li class="border-b border-gray-200">
                        <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $province->slug]) }}"
                           class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors flex justify-between items-center">
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
                        <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $province->slug]) }}"
                           class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors flex justify-between items-center">
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
    @endif

    <!-- Statistics Section -->
    <div class="sidebar-section">
        <div class="sidebar-header">Thống kê loto</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="{{ route('statistics.overdue') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê loto gan</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê chu kỳ dàn loto</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê nhanh</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('statistics.head-tail') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê đầu đuôi loto</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('statistics.frequency') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê tần suất loto</a>
            </li>
            <li>
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê chu kỳ đặc biệt</a>
            </li>
        </ul>
    </div>

    <!-- Lịch mở thưởng -->
    <div class="sidebar-section">
        <div class="sidebar-header">Lịch mở thưởng</div>
        <ul class="text-sm">
            <li>
                <a href="{{ route('schedule') }}" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Lịch quay xổ số</a>
            </li>
        </ul>
    </div>

    <!-- More Statistics -->
    <div class="sidebar-section">
        <div class="sidebar-header">Thống kê khác</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Tổng hợp chu kỳ đặc biệt</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Chu kỳ max dàn cùng về</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Giải đặc biệt gan</a>
            </li>
            <li class="border-b border-gray-200">
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Tần suất bộ số</a>
            </li>
            <li>
                <a href="#" class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">Thống kê tổng</a>
            </li>
        </ul>
    </div>
</div>
