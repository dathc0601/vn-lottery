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
@endphp

<div class="result-card border border-gray-300 bg-white mb-5" id="result-{{ $result->id }}">
    <!-- Yellow Header with Breadcrumb -->
    <div class="result-header-yellow bg-[#fff8dc] px-4 py-3 border-b border-gray-300">
        <h2 class="text-lg font-semibold text-center text-gray-800">
            XS{{ strtoupper($region) }} - Kết quả Xổ số {{ $regionName }} - SX{{ strtoupper($region) }} hôm nay
        </h2>
        <div class="text-center text-sm text-[#0066cc] mt-1">
            <a href="/{{ $region }}" class="hover:underline">XS{{ strtoupper($region) }}</a> /
            <a href="#" class="hover:underline">XS{{ strtoupper($region) }} {{ $dayOfWeek }}</a> /
            <a href="#" class="hover:underline">XS{{ strtoupper($region) }} {{ $drawDate }}</a>
        </div>
    </div>

    <!-- Draw Code -->
    @if($result->turn_num)
    <div class="px-4 py-3 bg-white text-center text-sm font-medium border-b border-gray-200">
        <span class="text-gray-600">Mã:</span> {{ $result->turn_num }}
    </div>
    @endif

    <!-- Prize Table -->
    <div class="px-4 py-4">
        <table class="result-table-xskt">
            <thead>
                <tr>
                    <th class="border border-gray-300 py-2 w-1/5">Giải thưởng</th>
                    <th class="border border-gray-300 py-2">Kết quả</th>
                </tr>
            </thead>
            <tbody>
                <!-- ĐB - Special Prize -->
                <tr>
                    <td class="border border-gray-300 py-3 font-semibold text-center">ĐB</td>
                    <td class="border border-gray-300 py-3 text-center">
                        <span class="font-bold text-xl text-red-600 number">{{ $result->prize_special }}</span>
                    </td>
                </tr>

                <!-- G1 -->
                <tr class="bg-gray-50">
                    <td class="border border-gray-300 py-2 font-semibold text-center">G1</td>
                    <td class="border border-gray-300 py-2 text-center">
                        <span class="font-semibold text-lg number">{{ $result->prize_1 }}</span>
                    </td>
                </tr>

                <!-- G2 -->
                @if($result->prize_2)
                <tr>
                    <td class="border border-gray-300 py-2 font-semibold text-center">G2</td>
                    <td class="border border-gray-300 py-2 text-center">
                        @php $prizes = explode(',', $result->prize_2); @endphp
                        @foreach($prizes as $prize)
                            <span class="font-medium number mx-2">{{ trim($prize) }}</span>
                        @endforeach
                    </td>
                </tr>
                @endif

                <!-- G3 -->
                @if($result->prize_3)
                <tr class="bg-gray-50">
                    <td class="border border-gray-300 py-2 font-semibold text-center">G3</td>
                    <td class="border border-gray-300 py-2 text-center">
                        @php $prizes = explode(',', $result->prize_3); @endphp
                        @foreach($prizes as $prize)
                            <span class="number mx-2">{{ trim($prize) }}</span>
                        @endforeach
                    </td>
                </tr>
                @endif

                <!-- G4 -->
                @if($result->prize_4)
                <tr>
                    <td class="border border-gray-300 py-2 font-semibold text-center">G4</td>
                    <td class="border border-gray-300 py-2 text-center">
                        @php $prizes = explode(',', $result->prize_4); @endphp
                        @foreach($prizes as $prize)
                            <span class="number mx-2">{{ trim($prize) }}</span>
                        @endforeach
                    </td>
                </tr>
                @endif

                <!-- G5 -->
                @if($result->prize_5)
                <tr class="bg-gray-50">
                    <td class="border border-gray-300 py-2 font-semibold text-center">G5</td>
                    <td class="border border-gray-300 py-2 text-center">
                        @php $prizes = explode(',', $result->prize_5); @endphp
                        @foreach($prizes as $prize)
                            <span class="number mx-2">{{ trim($prize) }}</span>
                        @endforeach
                    </td>
                </tr>
                @endif

                <!-- G6 -->
                @if($result->prize_6)
                <tr>
                    <td class="border border-gray-300 py-2 font-semibold text-center">G6</td>
                    <td class="border border-gray-300 py-2 text-center">
                        @php $prizes = explode(',', $result->prize_6); @endphp
                        @foreach($prizes as $prize)
                            <span class="number mx-2">{{ trim($prize) }}</span>
                        @endforeach
                    </td>
                </tr>
                @endif

                <!-- G7 -->
                @if($result->prize_7)
                <tr class="bg-gray-50">
                    <td class="border border-gray-300 py-2 font-semibold text-center">G7</td>
                    <td class="border border-gray-300 py-2 text-center">
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
    <div class="px-4 pb-3 flex items-center space-x-4">
        <label class="flex items-center cursor-pointer">
            <input type="radio" name="digit-display-{{ $result->id }}" value="all" checked class="mr-2">
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

    <!-- Statistics Links -->
    <div class="px-4 pb-3 text-sm">
        <a href="#" class="text-[#0066cc] hover:underline">Bảng loto {{ $regionName }}</a>
        <span class="text-gray-400 mx-1">/</span>
        <a href="#" class="text-[#0066cc] hover:underline">Lô XS{{ strtoupper($region) }} {{ $dayOfWeek }}</a>
    </div>

    <!-- Đầu/Đuôi Analysis Table -->
    <div class="px-4 pb-4">
        <h3 class="font-semibold text-gray-800 mb-2">Đầu/Đuôi loto {{ $regionName }}</h3>
        <table class="analysis-table border-collapse border border-gray-300">
            <thead>
                <tr>
                    <th class="border border-gray-300 py-2 bg-gray-200 w-16">Đầu</th>
                    <th class="border border-gray-300 py-2 bg-gray-200">Lô tô</th>
                    <th class="border border-gray-300 py-2 bg-gray-200 w-16">Đuôi</th>
                    <th class="border border-gray-300 py-2 bg-gray-200">Lô tô</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 10; $i++)
                <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : '' }}">
                    <td class="border border-gray-300 py-2 text-center font-semibold">{{ $i }}</td>
                    <td class="border border-gray-300 py-2 px-3 number text-sm">
                        {{ LotteryHelper::getHeadNumbers($result, $i) }}
                    </td>
                    <td class="border border-gray-300 py-2 text-center font-semibold">{{ $i }}</td>
                    <td class="border border-gray-300 py-2 px-3 number text-sm">
                        {{ LotteryHelper::getTailNumbers($result, $i) }}
                    </td>
                </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
// Handle digit display radio buttons
document.querySelectorAll('input[name="digit-display-{{ $result->id }}"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const resultCard = document.getElementById('result-{{ $result->id }}');
        const displayType = this.value;
        const numbers = resultCard.querySelectorAll('.number');

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
