@props(['result', 'region' => 'xsmb'])

@php
    use App\Helpers\LotteryHelper;

    // Get region name in Vietnamese
    $regionNames = [
        'xsmb' => 'Miền Bắc',
        'xsmt' => 'Miền Trung',
        'xsmn' => 'Miền Nam',
    ];
    $regionName = $regionNames[$region] ?? $region;

    // Format date
    $drawDate = $result->draw_date->format('d/m/Y');
    $dayOfWeek = LotteryHelper::getVietnameseDayOfWeek($result->draw_date);

    // Extract all last 2 digits and group by head digit for loto table
    $allNumbers = [];
    foreach(['prize_special', 'prize_1', 'prize_2', 'prize_3', 'prize_4', 'prize_5', 'prize_6', 'prize_7'] as $prize) {
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

    // Group by head digit (first digit of last 2)
    $lotoByHead = [];
    for($i = 0; $i <= 9; $i++) {
        $lotoByHead[$i] = [];
    }
    foreach($allNumbers as $num) {
        $head = intval($num[0]);
        $lotoByHead[$head][] = $num;
    }
@endphp

<div class="result-card border border-gray-300 bg-white mb-5" id="result-{{ $result->id }}">
    <!-- Yellow Header with Breadcrumb -->
    <div class="result-header-yellow bg-[#fff8dc] px-4 py-3 border-b border-gray-300">
        <h2 class="text-lg font-semibold text-center text-gray-800">
            XS{{ strtoupper($region) }} {{ $dayOfWeek }}, {{ $drawDate }}
        </h2>
        <div class="text-center text-sm text-[#0066cc] mt-1">
            <a href="/{{ $region }}" class="hover:underline">XS{{ strtoupper($region) }}</a> /
            <a href="#" class="hover:underline">XS{{ strtoupper($region) }} {{ $dayOfWeek }}</a> /
            <a href="#" class="hover:underline">XS{{ strtoupper($region) }} {{ $drawDate }}</a>
        </div>
    </div>

    <!-- Two-Column Layout: Prize Table + Loto Table -->
    <div class="flex flex-col lg:flex-row">
        <!-- Left Column: Prize Table -->
        <div class="flex-1 lg:border-r border-gray-300">
            <!-- Draw Code -->
            @if($result->turn_num)
            <div class="px-4 py-2 bg-white text-center text-sm font-medium border-b border-gray-200">
                <span class="text-gray-600">{{ $result->turn_num }}</span>
            </div>
            @endif

            <!-- Prize Table -->
            <div class="p-4">
                <table class="result-table-xskt w-full">
                    <tbody>
                        <!-- ĐB - Special Prize -->
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center w-12 bg-[#fff8dc]">ĐB</td>
                            <td class="py-2 text-center">
                                <span class="font-bold text-2xl text-red-600 number">{{ $result->prize_special }}</span>
                            </td>
                        </tr>

                        <!-- G1 -->
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center bg-gray-50">G1</td>
                            <td class="py-2 text-center">
                                <span class="font-semibold text-lg number">{{ $result->prize_1 }}</span>
                            </td>
                        </tr>

                        <!-- G2 -->
                        @if($result->prize_2)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center bg-gray-50">G2</td>
                            <td class="py-2 text-center">
                                @php $prizes = explode(',', $result->prize_2); @endphp
                                @foreach($prizes as $prize)
                                    <span class="font-medium number mx-3">{{ trim($prize) }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif

                        <!-- G3 -->
                        @if($result->prize_3)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center bg-gray-50" rowspan="{{ $region == 'xsmb' ? '2' : '1' }}">G3</td>
                            <td class="py-2 text-center">
                                @php $prizes = explode(',', $result->prize_3); @endphp
                                @if($region == 'xsmb' && count($prizes) > 3)
                                    @foreach(array_slice($prizes, 0, 3) as $prize)
                                        <span class="number mx-2">{{ trim($prize) }}</span>
                                    @endforeach
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 text-center">
                                    @foreach(array_slice($prizes, 3) as $prize)
                                        <span class="number mx-2">{{ trim($prize) }}</span>
                                    @endforeach
                                @else
                                    @foreach($prizes as $prize)
                                        <span class="number mx-2">{{ trim($prize) }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        @endif

                        <!-- G4 -->
                        @if($result->prize_4)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center bg-gray-50">G4</td>
                            <td class="py-2 text-center">
                                @php $prizes = explode(',', $result->prize_4); @endphp
                                @foreach($prizes as $prize)
                                    <span class="number mx-2">{{ trim($prize) }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif

                        <!-- G5 -->
                        @if($result->prize_5)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center bg-gray-50" rowspan="{{ $region == 'xsmb' && count(explode(',', $result->prize_5)) > 3 ? '2' : '1' }}">G5</td>
                            <td class="py-2 text-center">
                                @php $prizes = explode(',', $result->prize_5); @endphp
                                @if($region == 'xsmb' && count($prizes) > 3)
                                    @foreach(array_slice($prizes, 0, 3) as $prize)
                                        <span class="number mx-2">{{ trim($prize) }}</span>
                                    @endforeach
                            </td>
                        </tr>
                        <tr class="border-b border-gray-200">
                            <td class="py-2 text-center">
                                    @foreach(array_slice($prizes, 3) as $prize)
                                        <span class="number mx-2">{{ trim($prize) }}</span>
                                    @endforeach
                                @else
                                    @foreach($prizes as $prize)
                                        <span class="number mx-2">{{ trim($prize) }}</span>
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        @endif

                        <!-- G6 -->
                        @if($result->prize_6)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 font-semibold text-center bg-gray-50">G6</td>
                            <td class="py-2 text-center">
                                @php $prizes = explode(',', $result->prize_6); @endphp
                                @foreach($prizes as $prize)
                                    <span class="number mx-2">{{ trim($prize) }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif

                        <!-- G7 -->
                        @if($result->prize_7)
                        <tr>
                            <td class="py-2 font-semibold text-center bg-gray-50">G7</td>
                            <td class="py-2 text-center">
                                @php $prizes = explode(',', $result->prize_7); @endphp
                                @foreach($prizes as $prize)
                                    <span class="font-bold text-red-600 number mx-2">{{ trim($prize) }}</span>
                                @endforeach
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Radio Buttons for Digit Display -->
            <div class="px-4 pb-3 flex items-center space-x-4 border-t border-gray-200 pt-2">
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="digit-display-{{ $result->id }}" value="all" checked class="mr-2 accent-[#0066cc]">
                    <span class="text-sm">Tất cả</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="digit-display-{{ $result->id }}" value="2" class="mr-2">
                    <span class="text-sm">2 số cuối</span>
                </label>
                <label class="flex items-center cursor-pointer">
                    <input type="radio" name="digit-display-{{ $result->id }}" value="3" class="mr-2">
                    <span class="text-sm">3 số cuối</span>
                </label>
            </div>
        </div>

        <!-- Right Column: Loto Table -->
        <div class="lg:w-[280px] p-4 border-t lg:border-t-0 border-gray-300">
            <!-- Loto Links -->
            <div class="text-sm mb-3">
                <a href="#" class="text-[#0066cc] hover:underline">Bảng loto {{ $regionName }}</a>
                <span class="text-gray-400 mx-1">/</span>
                <a href="#" class="text-[#0066cc] hover:underline">Lô XS{{ strtoupper($region) }} {{ $dayOfWeek }}</a>
            </div>

            <!-- Loto Table (Đầu only) -->
            <table class="w-full border-collapse border border-gray-300 text-sm">
                <thead>
                    <tr>
                        <th class="border border-gray-300 py-2 bg-gray-100 w-12">Đầu</th>
                        <th class="border border-gray-300 py-2 bg-gray-100">Lô tô</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 0; $i <= 9; $i++)
                    <tr>
                        <td class="border border-gray-300 py-1 text-center font-semibold bg-gray-50">{{ $i }}</td>
                        <td class="border border-gray-300 py-1 px-2">
                            @if(count($lotoByHead[$i]) > 0)
                                @foreach($lotoByHead[$i] as $idx => $num)
                                    @if($num == substr($result->prize_special, -2))
                                        <span class="text-red-600 font-bold">{{ $num }}</span>@if($idx < count($lotoByHead[$i]) - 1); @endif
                                    @else
                                        {{ $num }}@if($idx < count($lotoByHead[$i]) - 1); @endif
                                    @endif
                                @endforeach
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Handle digit display radio buttons
document.querySelectorAll('input[name="digit-display-{{ $result->id }}"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const resultCard = document.getElementById('result-{{ $result->id }}');
        const displayType = this.value;
        const numbers = resultCard.querySelectorAll('.result-table-xskt .number');

        numbers.forEach(numberSpan => {
            const originalNumber = numberSpan.getAttribute('data-original') || numberSpan.textContent.trim();

            // Store original if not stored yet
            if (!numberSpan.getAttribute('data-original')) {
                numberSpan.setAttribute('data-original', originalNumber);
            }

            if (displayType === 'all') {
                numberSpan.textContent = originalNumber;
            } else if (displayType === '2') {
                // Show last 2 digits
                if (originalNumber.length >= 2) {
                    numberSpan.textContent = originalNumber.slice(-2);
                }
            } else if (displayType === '3') {
                // Show last 3 digits
                if (originalNumber.length >= 3) {
                    numberSpan.textContent = originalNumber.slice(-3);
                }
            }
        });
    });
});
</script>
@endpush
