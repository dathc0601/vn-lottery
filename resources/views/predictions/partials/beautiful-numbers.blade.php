@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');

    $regionNames = [
        'xsmb' => 'Miền Bắc',
        'xsmt' => 'Miền Trung',
        'xsmn' => 'Miền Nam',
    ];
    $regionName = $regionNames[$regionSlug] ?? strtoupper($regionSlug);

    $sections = [
        ['key' => 'bach_thu', 'title' => 'Vị trí bạch thủ chạy nhiều ngày nhất'],
        ['key' => 'lat_lien_tuc', 'title' => 'Lật liên tục nhiều ngày'],
        ['key' => 'cau_2_nhay', 'title' => 'Cầu 2 nháy'],
        ['key' => 'pascal_triangle', 'title' => 'Tam giác Pascal ' . $formattedDate],
        ['key' => 'lo_kep', 'title' => 'Cầu lô kẹp'],
        ['key' => 'loto_hay_ve', 'title' => 'Lô tô hay về (30 kỳ)'],
    ];
@endphp

<div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
    <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="text-green-600">✅</span>
            Dàn số đẹp Soi Cầu {{ $regionName }} ngày {{ $formattedDate }}
        </h2>
    </div>

    <div class="p-4 space-y-4">
        @foreach($sections as $section)
            @php
                $numbers = $analysisData[$section['key']] ?? [];
            @endphp

            @if(!empty($numbers))
            <div class="flex items-start gap-2">
                <span class="text-amber-500 mt-0.5">☆</span>
                <div>
                    <span class="text-gray-700">{{ $section['title'] }}: </span>
                    <span class="font-bold text-red-600">{{ implode(' - ', $numbers) }}</span>
                </div>
            </div>
            @endif
        @endforeach
    </div>
</div>
