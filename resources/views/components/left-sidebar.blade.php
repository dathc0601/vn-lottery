<!-- KQXSMB Section -->
<div class="sidebar-section mb-3">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        KQXSMB
    </div>
    <ul class="text-sm">
        @forelse($northProvinces as $province)
        <li class="border-b border-gray-200">
            <a href="{{ route('province.detail', ['region' => 'xsmb', 'slug' => $province->slug]) }}"
               class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                {{ $province->name }}
            </a>
        </li>
        @empty
        <li class="border-b border-gray-200">
            <a href="{{ route('xsmb') }}" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Hà Nội
            </a>
        </li>
        @endforelse
        <li class="border-b border-gray-200">
            <a href="{{ route('vietlott') }}" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Điện toán
            </a>
        </li>
        <li class="border-b border-gray-200">
            <a href="{{ route('vietlott') }}" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
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
        @forelse($centralProvinces as $province)
        <li class="border-b border-gray-200">
            <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $province->slug]) }}"
               class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                {{ $province->name }}
            </a>
        </li>
        @empty
        <li class="border-b border-gray-200">
            <a href="{{ route('xsmt') }}" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Miền Trung
            </a>
        </li>
        @endforelse
    </ul>
</div>

<!-- KQXSMN Section -->
<div class="sidebar-section mb-3">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        KQXSMN
    </div>
    <ul class="text-sm">
        @forelse($southProvinces as $province)
        <li class="border-b border-gray-200">
            <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $province->slug]) }}"
               class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                {{ $province->name }}
            </a>
        </li>
        @empty
        <li class="border-b border-gray-200">
            <a href="{{ route('xsmn') }}" class="block py-2 px-3 hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                Miền Nam
            </a>
        </li>
        @endforelse
    </ul>
</div>

<!-- Day Navigation -->
<div class="sidebar-section">
    <div class="quick-nav-tab bg-[#fff8dc] font-semibold text-center">
        Lịch mở thưởng
    </div>
    <ul class="text-sm">
        @php
            $currentDay = now()->dayOfWeek;
        @endphp
        @foreach($days as $day)
        <li class="border-b border-gray-200">
            <a href="{{ route('schedule') }}?day={{ $day['value'] }}"
               class="block py-2 px-3 hover:bg-gray-50 hover:text-[#ff6600] transition-colors {{ $currentDay == $day['value'] ? 'bg-[#fff8dc] font-semibold' : '' }}">
                {{ $day['label'] }}
            </a>
        </li>
        @endforeach
    </ul>
</div>
