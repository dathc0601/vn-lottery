@php
    $regionNames = [
        'xsmb' => 'Miền Bắc',
        'xsmt' => 'Miền Trung',
        'xsmn' => 'Miền Nam',
    ];
    $colors = [
        'xsmb' => '#c0392b',
        'xsmt' => '#d4a017',
        'xsmn' => '#27ae60',
    ];
    $thumbRegionName = $regionNames[$thumbSlug] ?? $thumbSlug;
    $thumbLabel = strtoupper($thumbSlug);
    $dateSlug = str_replace('/', '-', $thumbDate);
    $ogImageUrl = route('og-image.prediction', ['regionSlug' => $thumbSlug, 'date' => $dateSlug]);
    $bgColor = $colors[$thumbSlug] ?? '#c0392b';
@endphp
<img
    src="{{ $ogImageUrl }}"
    alt="Dự đoán {{ $thumbLabel }} {{ $thumbDate }}"
    width="200"
    height="130"
    loading="lazy"
    class="w-[200px] h-[130px] rounded object-cover"
    style="background-color: {{ $bgColor }}"
>
