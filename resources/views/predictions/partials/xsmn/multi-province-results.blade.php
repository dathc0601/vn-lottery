@php
    $dateFormatted = $referenceDate instanceof \Carbon\Carbon
        ? $referenceDate->format('d/m/Y')
        : date('d/m/Y', strtotime($referenceDate));

    $provinces = $provinces ?? collect();
    $results = $results ?? [];

    // Dynamic region configuration
    $regionCode = strtoupper($regionSlug ?? 'xsmn');

    // Index results by province_id for easy lookup
    $resultsByProvinceId = collect($results)->keyBy('province_id');

    // Define prize structure for XSMN/XSMT (G.8 to G.ĐB)
    $prizes = [
        ['key' => 'prize_8', 'label' => 'G.8', 'highlight' => false, 'text_size' => 'text-xl'],
        ['key' => 'prize_7', 'label' => 'G.7', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_6', 'label' => 'G.6', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_5', 'label' => 'G.5', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_4', 'label' => 'G.4', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_3', 'label' => 'G.3', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_2', 'label' => 'G.2', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_1', 'label' => 'G.1', 'highlight' => false, 'text_size' => 'text-base'],
        ['key' => 'prize_special', 'label' => 'G.ĐB', 'highlight' => true, 'text_size' => 'text-2xl'],
    ];
@endphp

@if($provinces->count() > 0)
<div class="my-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Kết quả {{ $regionCode }} ngày {{ $dateFormatted }}
    </h2>

    <div class="bg-white rounded shadow overflow-hidden overflow-x-auto">
        <table class="w-full border-collapse min-w-[600px]">
            <thead>
                <tr class="bg-blue-100">
                    <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 w-16">Giải</th>
                    @foreach($provinces as $province)
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-blue-700">
                            {{ $province->name }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($prizes as $prize)
                    <tr class="border-b border-gray-200 last:border-b-0 {{ $prize['highlight'] ? 'bg-yellow-50' : '' }}">
                        <td class="border border-gray-300 px-3 py-2 bg-gray-50 text-sm font-semibold text-gray-700 text-center">
                            {{ $prize['label'] }}
                        </td>
                        @foreach($provinces as $province)
                            @php
                                $result = $resultsByProvinceId[$province->id] ?? null;
                                $prizeValue = $result[$prize['key']] ?? '-';
                                // Replace comma with line break for multiple numbers
                                $displayValues = is_string($prizeValue) ? explode(',', $prizeValue) : [$prizeValue];
                            @endphp
                            <td class="border border-gray-300 px-2 py-2 text-center {{ $prize['highlight'] ? 'bg-yellow-50' : '' }}">
                                @foreach($displayValues as $val)
                                    @php $val = trim($val); @endphp
                                    @if($prize['highlight'])
                                        <span class="text-red-600 font-bold {{ $prize['text_size'] }}">{{ $val }}</span>
                                    @else
                                        @if($prize['key'] === 'prize_8')
                                            <span class="text-red-600 font-bold {{ $prize['text_size'] }}">{{ $val }}</span>
                                        @else
                                            <span class="text-gray-800 font-medium {{ $prize['text_size'] }}">{{ $val }}</span>
                                        @endif
                                    @endif
                                    @if(!$loop->last)<br>@endif
                                @endforeach
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
