@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
    $provinces = $provinces ?? collect();

    // Dynamic region configuration
    $regionCode = strtoupper($regionSlug ?? 'xsmn');
    $isXSMT = ($regionSlug ?? 'xsmn') === 'xsmt';
    $regionFullName = $isXSMT ? 'Miền Trung' : 'Miền Nam';
@endphp

<div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
    <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="text-green-600">&#10004;</span>
            Quay thử {{ $regionCode }} ngày {{ $formattedDate }} lấy lộc thần tài VIP
        </h2>
    </div>

    <div class="p-4">
        <p class="text-gray-700 mb-4">
            Thử vận may của bạn với công cụ quay thử xổ số miễn phí. Xem kết quả ngay lập tức!
        </p>

        @if($provinces->count() > 0)
        <div class="mb-4">
            <p class="text-sm text-gray-600 mb-2">
                <strong>Các đài hôm nay:</strong>
            </p>
            <div class="flex flex-wrap gap-2">
                @foreach($provinces as $province)
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
                        {{ $province->name }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        <a href="{{ $trialDrawUrl }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-500 to-red-500 text-white font-medium rounded hover:from-orange-600 hover:to-red-600 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Quay thử {{ $regionFullName }}
        </a>
    </div>
</div>
