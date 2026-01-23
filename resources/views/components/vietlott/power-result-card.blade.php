@props(['result'])

@php
    use App\Helpers\LotteryHelper;

    $dayOfWeek = [
        0 => 'Chủ nhật',
        1 => 'Thứ 2',
        2 => 'Thứ 3',
        3 => 'Thứ 4',
        4 => 'Thứ 5',
        5 => 'Thứ 6',
        6 => 'Thứ 7',
    ];
    $drawDate = $result->draw_date;
    $dayName = $dayOfWeek[$drawDate->dayOfWeek];
    $numbers = $result->winning_numbers;
    $mainNumbers = array_slice($numbers, 0, 6);
    $specialNumber = $numbers[6] ?? null;
@endphp

<div class="vietlott-result-card">
    <!-- Header -->
    <div class="vietlott-card-header">
        <div class="flex items-center justify-between">
            <div>
                <span class="font-semibold">{{ $dayName }}, {{ $drawDate->format('d/m/Y') }}</span>
                <span class="text-gray-500 ml-2">#{{ $result->draw_number }}</span>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-4">
        <!-- Dual Jackpot Display -->
        @if($result->jackpot_amount > 0)
            <div class="grid grid-cols-2 gap-2 mb-4">
                <div class="vietlott-jackpot-box">
                    <span class="text-xs">Jackpot 1:</span>
                    <span class="font-bold">{{ LotteryHelper::formatVietnameseCurrency($result->jackpot_amount) }}</span>
                </div>
                <div class="vietlott-jackpot-box-secondary">
                    <span class="text-xs">Jackpot 2:</span>
                    <span class="font-bold">Tích lũy</span>
                </div>
            </div>
        @endif

        <!-- Winning Numbers -->
        <div class="flex flex-wrap justify-center gap-2 mb-4">
            @foreach($mainNumbers as $number)
                <x-vietlott.number-ball :number="$number" />
            @endforeach
            @if($specialNumber)
                <x-vietlott.number-ball :number="$specialNumber" variant="special" />
            @endif
        </div>

        <!-- Prize Table -->
        <table class="vietlott-prize-table">
            <thead>
                <tr>
                    <th>Giải</th>
                    <th>Trúng khớp</th>
                    <th>Số lượng</th>
                    <th>Giá trị</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jackpot 1</td>
                    <td>6 số</td>
                    <td>-</td>
                    <td class="text-[#ff6600] font-bold">{{ $result->jackpot_amount > 0 ? LotteryHelper::formatVietnameseCurrency($result->jackpot_amount) : 'Tích lũy' }}</td>
                </tr>
                <tr>
                    <td>Jackpot 2</td>
                    <td>6 số + số đặc biệt</td>
                    <td>-</td>
                    <td class="text-[#ff6600] font-bold">Tích lũy</td>
                </tr>
                <tr>
                    <td>Giải Nhất</td>
                    <td>5 số</td>
                    <td>-</td>
                    <td>40 triệu</td>
                </tr>
                <tr>
                    <td>Giải Nhì</td>
                    <td>4 số</td>
                    <td>-</td>
                    <td>500k</td>
                </tr>
                <tr>
                    <td>Giải Ba</td>
                    <td>3 số</td>
                    <td>-</td>
                    <td>50k</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
