@php
    $topFrequency = $statistics['top_frequency_30d'] ?? [];
    $topGap = $statistics['top_gap'] ?? [];
    $provinces = $provinces ?? collect();

    // Get per-province statistics if available
    $perProvinceStats = $statistics['per_province'] ?? [];

    // Calculate max values for progress bars
    $maxFrequency = !empty($topFrequency) ? max($topFrequency) : 1;
    $maxGap = !empty($topGap) ? max($topGap) : 1;

    // Dynamic region configuration
    $regionCode = strtoupper($regionSlug ?? 'xsmn');
@endphp

<div class="my-6 space-y-6">
    {{-- Last 10 Special Prizes with Province Column --}}
    @if($last10SpecialPrizes->count() > 0)
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Thống kê <span class="text-red-600 font-bold">giải đặc biệt {{ $regionCode }}</span> trong 10 kỳ quay gần nhất
            </h2>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full border-collapse min-w-[500px]">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Tỉnh</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Giải ĐB</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($last10SpecialPrizes->take(10) as $prizeData)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                {{ $prizeData->draw_date->format('d/m') }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                {{ $prizeData->province->name ?? $regionCode }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                @php
                                    $prize = $prizeData->prize_special;
                                    $prefix = substr($prize, 0, -2);
                                    $suffix = substr($prize, -2);
                                @endphp
                                {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Top 10 Frequency with Progress Bars --}}
    @if(!empty($topFrequency))
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Top 10 <span class="text-red-600 font-bold">số về nhiều nhất</span> (30 ngày)
            </h2>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full border-collapse min-w-[400px]">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 w-16">Số</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 w-20">Số lần</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 w-16">+</th>
                        <th class="border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">Biểu đồ</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $freqItems = collect($topFrequency)->take(10)->toArray();
                    @endphp
                    @foreach($freqItems as $number => $count)
                        @php
                            $percentage = ($count / $maxFrequency) * 100;
                        @endphp
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="font-bold text-red-600 text-lg">{{ $number }}</span>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                {{ $count }} lần
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-green-600 font-medium">
                                +{{ $count }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="bg-gray-200 h-4 rounded overflow-hidden">
                                    <div class="bg-green-500 h-4 rounded transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Top 10 Gap (Long overdue) with Progress Bars --}}
    @if(!empty($topGap))
    <div class="bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h2 class="text-base font-medium text-gray-900">
                Top 10 <span class="text-red-600 font-bold">số lâu chưa về</span>
            </h2>
        </div>
        <div class="p-4 overflow-x-auto">
            <table class="w-full border-collapse min-w-[400px]">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 w-16">Số</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700 w-24">Số ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700">Biểu đồ</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $gapItems = collect($topGap)->take(10)->toArray();
                    @endphp
                    @foreach($gapItems as $number => $days)
                        @php
                            $percentage = ($days / $maxGap) * 100;
                        @endphp
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center">
                                <span class="font-bold text-red-600 text-lg">{{ $number }}</span>
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                {{ $days }} ngày
                            </td>
                            <td class="border border-gray-300 px-3 py-2">
                                <div class="bg-gray-200 h-4 rounded overflow-hidden">
                                    <div class="bg-orange-500 h-4 rounded transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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
                Thống kê <span class="text-red-600 font-bold">giải đặc biệt XSMN</span> trong 30 kỳ quay gần nhất
            </h2>
        </div>
        <div class="p-4 max-h-80 overflow-y-auto overflow-x-auto">
            <table class="w-full border-collapse min-w-[500px]">
                <thead class="sticky top-0">
                    <tr class="bg-gray-50">
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Ngày</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Tỉnh</th>
                        <th class="border border-gray-300 px-3 py-2 text-center text-sm font-semibold text-gray-700">Giải ĐB</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($last30SpecialPrizes as $prizeData)
                        <tr>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                {{ $prizeData->draw_date->format('d/m') }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm text-gray-700">
                                {{ $prizeData->province->name ?? $regionCode }}
                            </td>
                            <td class="border border-gray-300 px-3 py-2 text-center text-sm">
                                @php
                                    $prize = $prizeData->prize_special;
                                    $prefix = substr($prize, 0, -2);
                                    $suffix = substr($prize, -2);
                                @endphp
                                {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
