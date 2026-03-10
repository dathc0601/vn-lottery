@php
    $regionNames = [
        'xsmb' => 'Miền Bắc',
        'xsmt' => 'Miền Trung',
        'xsmn' => 'Miền Nam',
    ];
    $colors = [
        'xsmb' => '#c0392b',
        'xsmt' => '#7e57c2',
        'xsmn' => '#27ae60',
    ];
    $thumbRegionName = $regionNames[$thumbSlug] ?? $thumbSlug;
    $thumbLabel = strtoupper($thumbSlug);
    // Extract date from thumbDateLine (e.g., "Thứ Bảy, 31/01/2026" → "31/01/2026")
    preg_match('/(\d{2}\/\d{2}\/\d{4})/', $thumbDateLine ?? '', $m);
    $dateSlug = str_replace('/', '-', $m[1] ?? '');
    $ogImageUrl = route('og-image.prediction', ['regionSlug' => $thumbSlug, 'date' => $dateSlug]);
    $bgColor = $colors[$thumbSlug] ?? '#c0392b';
@endphp
<img
    src="{{ $ogImageUrl }}"
    alt="Dự đoán {{ $thumbLabel }} {{ $thumbDateLine ?? '' }}"
    loading="lazy"
    class="w-full h-auto rounded object-cover aspect-[400/270]"
    style="background-color: {{ $bgColor }}"
>
