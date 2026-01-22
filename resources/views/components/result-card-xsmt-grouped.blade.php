@props(['dayGroup', 'region' => 'xsmt'])

@php
    use App\Helpers\LotteryHelper;

    $date = $dayGroup['date'];
    $provinces = $dayGroup['provinces'];
    $results = $dayGroup['results'];

    // Format date
    $drawDate = $date->format('d/m/Y');
    $dayOfWeek = LotteryHelper::getVietnameseDayOfWeek($date);

    // Prize order for XSMT (top to bottom): G8, G7, G6, G5, G4, G3, G2, G1, ĐB
    $prizeLabels = [
        'prize_8' => ['label' => 'G8', 'class' => 'text-base'],
        'prize_7' => ['label' => 'G7', 'class' => 'text-base'],
        'prize_6' => ['label' => 'G6', 'class' => 'text-base'],
        'prize_5' => ['label' => 'G5', 'class' => 'text-base'],
        'prize_4' => ['label' => 'G4', 'class' => 'text-base'],
        'prize_3' => ['label' => 'G3', 'class' => 'text-base'],
        'prize_2' => ['label' => 'G2', 'class' => 'text-base'],
        'prize_1' => ['label' => 'G1', 'class' => 'text-base'],
        'prize_special' => ['label' => 'ĐB', 'class' => 'text-lg font-bold text-red-600'],
    ];

    // Build loto data for each province
    $lotoData = [];
    foreach ($provinces as $province) {
        $result = $results[$province->id] ?? null;
        if ($result) {
            $allNumbers = [];
            foreach(['prize_special', 'prize_1', 'prize_2', 'prize_3', 'prize_4', 'prize_5', 'prize_6', 'prize_7', 'prize_8'] as $prize) {
                if($result->$prize) {
                    $numbers = explode(',', $result->$prize);
                    foreach($numbers as $num) {
                        $num = trim($num);
                        if(strlen($num) >= 2) {
                            $last2 = substr($num, -2);
                            $allNumbers[] = $last2;
                        }
                    }
                }
            }

            // Group by head digit
            $lotoByHead = [];
            for($i = 0; $i <= 9; $i++) {
                $lotoByHead[$i] = [];
            }
            foreach($allNumbers as $num) {
                $head = intval($num[0]);
                $lotoByHead[$head][] = $num;
            }
            $lotoData[$province->id] = [
                'lotoByHead' => $lotoByHead,
                'specialLast2' => $result->prize_special ? substr($result->prize_special, -2) : null,
            ];
        }
    }
@endphp

<div class="result-card-grouped border border-gray-300 bg-white mb-5" id="result-grouped-{{ $date->format('Y-m-d') }}">
    <!-- Yellow Header -->
    <div class="bg-[#fff8dc] px-4 py-3 border-b border-gray-300">
        <h2 class="text-lg font-semibold text-center text-gray-800">
            XSMT - Kết quả XS miền Trung - {{ $dayOfWeek }}, ngày {{ $drawDate }}
        </h2>
    </div>

    <!-- Province Tabs -->
    <div class="bg-gray-100 px-4 py-2 border-b border-gray-300 text-sm">
        <span class="text-gray-600">Tỉnh: </span>
        @foreach($provinces as $index => $province)
            <a href="{{ route('province.detail', ['region' => $region, 'slug' => $province->slug]) }}"
               class="text-[#0066cc] hover:underline font-medium">{{ $province->name }}</a>@if(!$loop->last)<span class="text-gray-400 mx-1">|</span>@endif
        @endforeach
    </div>

    <!-- Multi-column Prize Table -->
    <div class="overflow-x-auto">
        <table class="result-table-xsmt-grouped w-full border-collapse">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 py-2 px-3 text-center w-16">Giải</th>
                    @foreach($provinces as $province)
                        <th class="border border-gray-300 py-2 px-2 text-center min-w-[140px]">
                            <a href="{{ route('province.detail', ['region' => $region, 'slug' => $province->slug]) }}"
                               class="text-[#0066cc] hover:underline">{{ $province->name }}</a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($prizeLabels as $prizeKey => $prizeInfo)
                    <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                        <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100 {{ $prizeKey === 'prize_special' ? 'bg-[#fff8dc]' : '' }}">
                            {{ $prizeInfo['label'] }}
                        </td>
                        @foreach($provinces as $province)
                            @php
                                $result = $results[$province->id] ?? null;
                                $prizeValue = $result ? $result->$prizeKey : null;
                                $prizes = $prizeValue ? array_map('trim', explode(',', $prizeValue)) : [];
                            @endphp
                            <td class="border border-gray-300 py-2 px-2 text-center">
                                @if(count($prizes) > 0)
                                    <div class="flex flex-wrap justify-center gap-1">
                                        @foreach($prizes as $prize)
                                            <span class="number {{ $prizeInfo['class'] }} {{ $prizeKey === 'prize_special' ? 'text-red-600 font-bold' : '' }}">{{ $prize }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Loto Table Section -->
    <div class="border-t border-gray-300">
        <div class="bg-gray-100 px-4 py-2 border-b border-gray-300">
            <span class="font-semibold text-sm">Bảng Lô Tô</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 py-2 px-2 text-center w-12">Đầu</th>
                        @foreach($provinces as $province)
                            <th class="border border-gray-300 py-2 px-2 text-center min-w-[140px]">
                                {{ $province->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i <= 9; $i++)
                        <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : '' }}">
                            <td class="border border-gray-300 py-1 px-2 text-center font-semibold bg-gray-100">{{ $i }}</td>
                            @foreach($provinces as $province)
                                @php
                                    $provinceData = $lotoData[$province->id] ?? null;
                                    $headNumbers = $provinceData ? $provinceData['lotoByHead'][$i] : [];
                                    $specialLast2 = $provinceData ? $provinceData['specialLast2'] : null;
                                @endphp
                                <td class="border border-gray-300 py-1 px-2 text-center">
                                    @if(count($headNumbers) > 0)
                                        @foreach($headNumbers as $idx => $num)
                                            @if($num == $specialLast2)
                                                <span class="text-red-600 font-bold">{{ $num }}</span>@if($idx < count($headNumbers) - 1); @endif
                                            @else
                                                {{ $num }}@if($idx < count($headNumbers) - 1); @endif
                                            @endif
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
