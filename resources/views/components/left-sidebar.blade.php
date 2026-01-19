<!-- KQXSMB Section -->
<div class="sidebar-section mb-3">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        KQXSMB
    </div>
    <ul class="text-sm">
        <li class="border-b border-gray-200">
            <a href="{{ route('xsmb') }}" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Hà Nội
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Điện toán
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Max 3D
            </a>
        </li>
    </ul>
</div>

<!-- KQXSMT Section -->
<div class="sidebar-section mb-3">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        KQXSMT
    </div>
    <ul class="text-sm">
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Thừa Thiên Huế
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Phú Yên
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Đắk Lắk
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Quảng Nam
            </a>
        </li>
    </ul>
</div>

<!-- KQXSMN Section -->
<div class="sidebar-section mb-3">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        KQXSMN
    </div>
    <ul class="text-sm">
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                TP. Hồ Chí Minh
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Đồng Tháp
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Cà Mau
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Bến Tre
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Vũng Tàu
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Cần Thơ
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Đồng Nai
            </a>
        </li>
    </ul>
</div>

<!-- Day Navigation -->
<div class="sidebar-section">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        Lịch mở thưởng
    </div>
    <ul class="text-sm">
        @php
            $days = [
                ['label' => 'Thứ 2', 'value' => 1],
                ['label' => 'Thứ 3', 'value' => 2],
                ['label' => 'Thứ 4', 'value' => 3],
                ['label' => 'Thứ 5', 'value' => 4],
                ['label' => 'Thứ 6', 'value' => 5],
                ['label' => 'Thứ 7', 'value' => 6],
                ['label' => 'Chủ Nhật', 'value' => 0],
            ];
            $currentDay = now()->dayOfWeek;
        @endphp
        @foreach($days as $day)
        <li class="border-b border-gray-200">
            <a href="#" class="block py-2 px-3 hover:bg-gray-50 hover:text-[#ff6600] transition-colors {{ $currentDay == $day['value'] ? 'bg-[#fff8dc] font-semibold' : '' }}">
                {{ $day['label'] }}
            </a>
        </li>
        @endforeach
    </ul>
</div>
