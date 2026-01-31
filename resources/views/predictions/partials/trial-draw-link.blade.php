@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');

    $regionNames = [
        'xsmb' => 'Miền Bắc',
        'xsmt' => 'Miền Trung',
        'xsmn' => 'Miền Nam',
    ];
    $regionName = $regionNames[$regionSlug] ?? strtoupper($regionSlug);
@endphp

<div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
    <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="text-green-600">✅</span>
            Quay thử {{ strtoupper($regionSlug) }} ngày {{ $formattedDate }} lấy lộc thần tài VIP
        </h2>
    </div>

    <div class="p-4">
        <p class="text-gray-700 mb-3">
            Thử vận may của bạn với công cụ quay thử xổ số miễn phí. Xem kết quả ngay lập tức!
        </p>
        <a href="{{ $trialDrawUrl }}" class="text-blue-600 hover:text-blue-800 underline font-medium">
            Quay thử {{ $regionName }}
        </a>
    </div>
</div>
