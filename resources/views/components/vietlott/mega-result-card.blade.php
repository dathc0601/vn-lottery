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
        <!-- Jackpot Display -->
        @if($result->jackpot_amount > 0)
            <div class="vietlott-jackpot-box mb-4">
                <span class="text-sm">Jackpot:</span>
                <span class="font-bold text-lg">{{ LotteryHelper::formatVietnameseCurrency($result->jackpot_amount) }}</span>
            </div>
        @endif

        <!-- Winning Numbers -->
        <div class="flex flex-wrap justify-center gap-2 mb-4">
            @foreach($result->winning_numbers as $number)
                <x-vietlott.number-ball :number="$number" />
            @endforeach
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
                    <td>Jackpot</td>
                    <td>6 số</td>
                    <td>-</td>
                    <td class="text-[#ff6600] font-bold">{{ $result->jackpot_amount > 0 ? LotteryHelper::formatVietnameseCurrency($result->jackpot_amount) : 'Tích lũy' }}</td>
                </tr>
                <tr>
                    <td>Giải Nhất</td>
                    <td>5 số</td>
                    <td>-</td>
                    <td>10 triệu</td>
                </tr>
                <tr>
                    <td>Giải Nhì</td>
                    <td>4 số</td>
                    <td>-</td>
                    <td>300k</td>
                </tr>
                <tr>
                    <td>Giải Ba</td>
                    <td>3 số</td>
                    <td>-</td>
                    <td>30k</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
