{{-- Xổ Số Miền Nam --}}
<div class="sidebar-section">
    <div class="sidebar-header-dark">Xổ số Miền Nam</div>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-sm">
            @foreach($southProvinces as $province)
                <a href="{{ route('province.detail', ['code' => $province->code, 'slug' => $province->slug]) }}"
                   class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
                    <span class="text-gray-400 text-xs">&bull;</span> {{ $province->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Xổ Số Miền Trung --}}
<div class="sidebar-section">
    <div class="sidebar-header-dark">Xổ số Miền Trung</div>
    <div class="p-3">
        <div class="grid grid-cols-2 gap-x-2 gap-y-0.5 text-sm">
            @foreach($centralProvinces as $province)
                <a href="{{ route('province.detail', ['code' => $province->code, 'slug' => $province->slug]) }}"
                   class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
                    <span class="text-gray-400 text-xs">&bull;</span> {{ $province->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Xổ Số Miền Bắc --}}
<div class="sidebar-section">
    <div class="sidebar-header-dark">Xổ số Miền Bắc</div>
    <div class="p-3">
        <div class="text-sm space-y-0.5">
            @foreach($northProvinces as $province)
                <a href="{{ route('province.detail', ['code' => $province->code, 'slug' => $province->slug]) }}"
                   class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
                    <span class="text-gray-400 text-xs">&bull;</span> {{ $province->name }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Xổ Số Vietlott --}}
<div class="sidebar-section">
    <div class="sidebar-header-dark">Xổ số Vietlott</div>
    <div class="p-3 text-sm space-y-0.5">
        <a href="{{ route('vietlott.mega645') }}" class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
            <span class="text-gray-400 text-xs">&bull;</span> Mega 6/45
        </a>
        <a href="{{ route('vietlott.power655') }}" class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
            <span class="text-gray-400 text-xs">&bull;</span> Power 6/55
        </a>
        <a href="{{ route('vietlott.max3d') }}" class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
            <span class="text-gray-400 text-xs">&bull;</span> Max 3D
        </a>
        <a href="{{ route('vietlott.max3dpro') }}" class="text-[#0066cc] hover:text-[#cc0000] py-0.5 flex items-center gap-1">
            <span class="text-gray-400 text-xs">&bull;</span> Max 3D Pro
        </a>
    </div>
</div>
