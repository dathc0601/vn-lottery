@php
    $colors = [
        'xsmb' => '#c0392b',
        'xsmt' => '#d4a017',
        'xsmn' => '#27ae60',
    ];
    $regionNames = [
        'xsmb' => 'MIỀN BẮC',
        'xsmt' => 'MIỀN TRUNG',
        'xsmn' => 'MIỀN NAM',
    ];
    $bgColor = $colors[$thumbSlug] ?? '#c0392b';
    $thumbRegionName = $regionNames[$thumbSlug] ?? strtoupper($thumbSlug);
    $thumbLabel = strtoupper($thumbSlug);
    $thumbDate = isset($thumbDate) ? $thumbDate : null;
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="240" height="160" viewBox="0 0 240 160" class="w-[200px] h-[130px] rounded" role="img" aria-label="Dự đoán {{ $thumbLabel }} {{ $thumbDate }}">
    <rect width="240" height="160" rx="4" fill="{{ $bgColor }}"/>
    <text x="120" y="45" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="14" font-weight="bold">DỰ ĐOÁN KQXS</text>
    <text x="120" y="72" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="16" font-weight="bold">{{ $thumbRegionName }}</text>
    @if($thumbDate)
        <rect x="40" y="88" width="160" height="28" rx="4" fill="rgba(0,0,0,0.25)"/>
        <text x="120" y="107" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="15" font-weight="bold">{{ $thumbDate }}</text>
    @endif
    <text x="120" y="145" text-anchor="middle" fill="rgba(255,255,255,0.7)" font-family="Arial, sans-serif" font-size="12">{{ $thumbLabel }}</text>
</svg>
