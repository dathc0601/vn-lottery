@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
    $provinces = $provinces ?? collect();
    $provinceCount = $provinces->count();

    // Dynamic region configuration
    $regionCode = strtoupper($regionSlug ?? 'xsmn');

    $regionAggregate = $predictionsData['region_aggregate'] ?? [];
    $baoLo4Dai = $regionAggregate['bao_lo_4_dai'] ?? [];
    $xien2 = $regionAggregate['xien_2'] ?? [];
    $threeCangDep = $regionAggregate['3_cang_dep'] ?? [];
@endphp

<div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
    <div class="bg-gradient-to-r from-red-600 to-red-500 px-4 py-3">
        <h2 class="text-lg font-bold text-white flex items-center gap-2">
            <span>&#127942;</span>
            ĐẶC BIỆT {{ $provinceCount }} ĐÀI {{ $regionCode }} {{ $formattedDate }}
        </h2>
    </div>

    <div class="p-4 space-y-4">
        {{-- Bao lô 4 đài --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">&#9733;</span>
            <div>
                <span class="text-gray-700 font-medium">Bao lô {{ $provinceCount }} đài: </span>
                @if(!empty($baoLo4Dai))
                    <span class="font-bold text-red-600 text-lg">{{ implode(' - ', $baoLo4Dai) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>

        {{-- Lô xiên 2 --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">&#9733;</span>
            <div>
                <span class="text-gray-700 font-medium">Lô xiên 2: </span>
                @if(!empty($xien2))
                    <span class="font-bold text-red-600 text-lg">{{ implode(' - ', $xien2) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>

        {{-- 3 càng đẹp --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">&#9733;</span>
            <div>
                <span class="text-gray-700 font-medium">3 càng đẹp: </span>
                @if(!empty($threeCangDep))
                    <span class="font-bold text-red-600 text-lg">{{ implode(' - ', $threeCangDep) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>

        {{-- Province list note --}}
        @if($provinces->count() > 0)
        <div class="mt-4 pt-3 border-t border-gray-200">
            <p class="text-sm text-gray-600">
                <strong>Các đài hôm nay:</strong>
                {{ $provinces->pluck('name')->implode(', ') }}
            </p>
        </div>
        @endif
    </div>
</div>
