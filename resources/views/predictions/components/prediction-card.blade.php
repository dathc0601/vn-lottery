@php
    $predictionsData = $prediction->predictions_data ?? [];
    $loto2Digit = $predictionsData['loto_2_digit'] ?? [];
@endphp

<div class="bg-white rounded shadow overflow-hidden hover:shadow-lg transition-shadow">
    <div class="p-4">
        {{-- Date Badge --}}
        <div class="flex items-center justify-between mb-3">
            <span class="inline-block text-sm text-white bg-[#ff6600] px-3 py-1 rounded">
                {{ $prediction->formatted_date }}
            </span>
            <span class="text-xs text-gray-500">
                {{ strtoupper($prediction->region_slug) }}
            </span>
        </div>

        {{-- Title --}}
        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
            Soi cầu KQ{{ strtoupper($prediction->region_slug) }} {{ $prediction->formatted_date }}
        </h3>

        {{-- Preview Numbers --}}
        @if(!empty($loto2Digit))
            <div class="mb-3">
                <span class="text-xs text-gray-500 block mb-1">Loto 2 số hay về:</span>
                <div class="flex flex-wrap gap-1">
                    @foreach(array_slice($loto2Digit, 0, 5) as $num)
                        <span class="inline-block px-2 py-0.5 bg-orange-100 text-orange-800 rounded text-sm font-medium">
                            {{ $num }}
                        </span>
                    @endforeach
                    @if(count($loto2Digit) > 5)
                        <span class="text-gray-400 text-sm">+{{ count($loto2Digit) - 5 }}</span>
                    @endif
                </div>
            </div>
        @endif

        {{-- View Link --}}
        <a href="{{ $prediction->url }}"
           class="inline-flex items-center text-[#ff6600] hover:text-[#ff7700] font-medium text-sm">
            Xem chi tiết
            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
