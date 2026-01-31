@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
    $provinces = $provinces ?? collect();
    $provinceNames = $provinces->pluck('name')->toArray();
    $provinceCount = count($provinceNames);

    // Dynamic region configuration
    $regionCode = strtoupper($regionSlug ?? 'xsmn');
    $isXSMT = ($regionSlug ?? 'xsmn') === 'xsmt';
    $regionFullName = $isXSMT ? 'Miền Trung' : 'Miền Nam';

    // Get aggregate stats from analysis data
    $regionAggregate = $analysisData['region_aggregate'] ?? [];
    $loGanSummary = $regionAggregate['lo_gan_summary'] ?? [];
    $loNongSummary = $regionAggregate['lo_nong_summary'] ?? [];

    // Get top lo gan
    $topLoGan = !empty($loGanSummary) ? $loGanSummary[0] : null;
    // Get top lo nong
    $topLoNong = !empty($loNongSummary) ? $loNongSummary[0] : null;
@endphp

<div class="text-gray-700 mb-6 leading-relaxed">
    <p class="mb-3">
        <strong class="text-gray-900">Soi cầu {{ $regionCode }} ngày {{ $formattedDate }}</strong> - Chuyên trang dự đoán kết quả xổ số {{ $regionFullName }} hôm nay
        @if($provinceCount > 0)
            với <strong class="text-red-600">{{ $provinceCount }} đài</strong>:
            <span class="font-medium text-blue-700">{{ implode(', ', $provinceNames) }}</span>.
        @endif
    </p>

    @if($topLoGan || $topLoNong)
    <p class="mb-3">
        Theo thống kê,
        @if($topLoGan)
            lô gan lâu nhất hôm nay là <span class="font-bold text-red-600">{{ $topLoGan['number'] }}</span>
            ({{ $topLoGan['province'] ?? $regionCode }}, {{ $topLoGan['days'] ?? 0 }} ngày chưa về)@if($topLoNong), @else.@endif
        @endif
        @if($topLoNong)
            số nóng nhất là <span class="font-bold text-red-600">{{ $topLoNong['number'] }}</span>
            ({{ $topLoNong['province'] ?? $regionCode }}, {{ $topLoNong['times'] ?? 0 }} lần trong 30 ngày).
        @endif
    </p>
    @endif

    <p>
        Các con số được tính toán dựa trên phân tích thống kê từ kết quả các kỳ quay trước.
        Tham khảo soi cầu {{ $regionCode }} {{ $formattedDate }} để có thêm thông tin hữu ích cho việc chọn số.
    </p>
</div>
