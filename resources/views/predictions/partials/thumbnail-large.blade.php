@php
    $colors = [
        'xsmb' => '#c0392b',
        'xsmt' => '#7e57c2',
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
@endphp
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 270" class="w-full h-auto rounded" role="img" aria-label="Dự đoán {{ $thumbLabel }} {{ $thumbDateLine ?? '' }}">
    <rect width="400" height="270" rx="6" fill="{{ $bgColor }}"/>
    <text x="200" y="70" text-anchor="middle" fill="rgba(255,255,255,0.85)" font-family="Arial, sans-serif" font-size="22" font-weight="bold">DỰ ĐOÁN KQXS</text>
    <text x="200" y="130" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="36" font-weight="bold">{{ $thumbRegionName }}</text>
    @if(!empty($thumbDateLine))
        <text x="200" y="190" text-anchor="middle" fill="rgba(255,255,255,0.9)" font-family="Arial, sans-serif" font-size="24" font-style="italic">{{ $thumbDateLine }}</text>
    @endif
    <text x="200" y="245" text-anchor="middle" fill="rgba(255,255,255,0.5)" font-family="Arial, sans-serif" font-size="16">{{ $thumbLabel }}</text>
</svg>
