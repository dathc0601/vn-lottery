@php
    $topFrequency = $statistics['top_frequency_30d'] ?? [];
    $topGap = $statistics['top_gap'] ?? [];

    $regionNames = [
        'xsmb' => 'Miền Bắc',
        'xsmt' => 'Miền Trung',
        'xsmn' => 'Miền Nam',
    ];
    $regionName = $regionNames[$regionSlug] ?? strtoupper($regionSlug);
@endphp

<div class="my-6 space-y-6">
    {{-- Last 10 Special Prizes - 2 column layout --}}
    @if($last10SpecialPrizes->count() > 0)
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Thống kê <span class="text-red-600 font-bold">giải đặc biệt {{ $regionName }}</span> trong 10 kỳ quay gần nhất
            </h2>
        </div>
        <div class="p-4">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Giải ĐB</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Giải ĐB</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $prizes = $last10SpecialPrizes->values();
                        $halfCount = ceil($prizes->count() / 2);
                    @endphp
                    @for($i = 0; $i < $halfCount; $i++)
                        <tr>
                            {{-- Left column --}}
                            @if(isset($prizes[$i]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                    {{ $prizes[$i]->draw_date->format('d/m') }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                    @php
                                        $prize = $prizes[$i]->prize_special;
                                        $prefix = substr($prize, 0, -2);
                                        $suffix = substr($prize, -2);
                                    @endphp
                                    {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                                </td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif

                            {{-- Right column --}}
                            @php $rightIndex = $i + $halfCount; @endphp
                            @if(isset($prizes[$rightIndex]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                    {{ $prizes[$rightIndex]->draw_date->format('d/m') }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                    @php
                                        $prize = $prizes[$rightIndex]->prize_special;
                                        $prefix = substr($prize, 0, -2);
                                        $suffix = substr($prize, -2);
                                    @endphp
                                    {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                                </td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Top 10 Frequency --}}
    @if(!empty($topFrequency))
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Top 10 <span class="text-red-600 font-bold">số về nhiều nhất</span> (30 ngày)
            </h2>
        </div>
        <div class="p-4">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">STT</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Số</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Tần suất</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">STT</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Số</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Tần suất</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $freqItems = collect($topFrequency)->take(10)->toArray();
                        $freqKeys = array_keys($freqItems);
                        $freqValues = array_values($freqItems);
                        $halfCount = ceil(count($freqItems) / 2);
                    @endphp
                    @for($i = 0; $i < $halfCount; $i++)
                        <tr>
                            {{-- Left column --}}
                            @if(isset($freqKeys[$i]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $i + 1 }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm font-bold text-red-600">{{ $freqKeys[$i] }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $freqValues[$i] }} lần</td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif

                            {{-- Right column --}}
                            @php $rightIndex = $i + $halfCount; @endphp
                            @if(isset($freqKeys[$rightIndex]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $rightIndex + 1 }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm font-bold text-red-600">{{ $freqKeys[$rightIndex] }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $freqValues[$rightIndex] }} lần</td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Top 10 Gap (Long overdue) --}}
    @if(!empty($topGap))
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Top 10 <span class="text-red-600 font-bold">số lâu chưa về</span>
            </h2>
        </div>
        <div class="p-4">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">STT</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Số</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Số ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">STT</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Số</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Số ngày</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $gapItems = collect($topGap)->take(10)->toArray();
                        $gapKeys = array_keys($gapItems);
                        $gapValues = array_values($gapItems);
                        $halfCount = ceil(count($gapItems) / 2);
                    @endphp
                    @for($i = 0; $i < $halfCount; $i++)
                        <tr>
                            {{-- Left column --}}
                            @if(isset($gapKeys[$i]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $i + 1 }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm font-bold text-red-600">{{ $gapKeys[$i] }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $gapValues[$i] }} ngày</td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif

                            {{-- Right column --}}
                            @php $rightIndex = $i + $halfCount; @endphp
                            @if(isset($gapKeys[$rightIndex]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $rightIndex + 1 }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm font-bold text-red-600">{{ $gapKeys[$rightIndex] }}</td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">{{ $gapValues[$rightIndex] }} ngày</td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Last 30 Special Prizes --}}
    @if($last30SpecialPrizes->count() > 10)
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Thống kê <span class="text-red-600 font-bold">giải đặc biệt {{ $regionName }}</span> trong 30 kỳ quay gần nhất
            </h2>
        </div>
        <div class="p-4 max-h-80 overflow-y-auto">
            <table class="w-full border-collapse">
                <thead class="sticky top-0">
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Giải ĐB</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Giải ĐB</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $prizes = $last30SpecialPrizes->values();
                        $halfCount = ceil($prizes->count() / 2);
                    @endphp
                    @for($i = 0; $i < $halfCount; $i++)
                        <tr>
                            {{-- Left column --}}
                            @if(isset($prizes[$i]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                    {{ $prizes[$i]->draw_date->format('d/m') }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                    @php
                                        $prize = $prizes[$i]->prize_special;
                                        $prefix = substr($prize, 0, -2);
                                        $suffix = substr($prize, -2);
                                    @endphp
                                    {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                                </td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif

                            {{-- Right column --}}
                            @php $rightIndex = $i + $halfCount; @endphp
                            @if(isset($prizes[$rightIndex]))
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                    {{ $prizes[$rightIndex]->draw_date->format('d/m') }}
                                </td>
                                <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                    @php
                                        $prize = $prizes[$rightIndex]->prize_special;
                                        $prefix = substr($prize, 0, -2);
                                        $suffix = substr($prize, -2);
                                    @endphp
                                    {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                                </td>
                            @else
                                <td class="border border-gray-300 px-3 py-2"></td>
                                <td class="border border-gray-300 px-3 py-2"></td>
                            @endif
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
