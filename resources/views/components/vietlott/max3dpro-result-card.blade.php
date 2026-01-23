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
    $prizes = $result->winning_numbers;
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
        <table class="vietlott-prize-table">
            <thead>
                <tr>
                    <th>Giải</th>
                    <th>Dãy số trúng</th>
                    <th>SL</th>
                    <th>Giá trị</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $prizeConfig = [
                        'Giải Đặc biệt' => ['label' => 'ĐB', 'value' => '2 tỷ'],
                        'Giải Phụ Đặc biệt' => ['label' => 'Phụ ĐB', 'value' => '400 triệu'],
                        'Giải Nhất' => ['label' => 'Nhất', 'value' => '30 triệu'],
                        'Giải Nhì' => ['label' => 'Nhì', 'value' => '10 triệu'],
                        'Giải Ba' => ['label' => 'Ba', 'value' => '5 triệu'],
                        'Giải Tư' => ['label' => 'Tư', 'value' => '1 triệu'],
                        'Giải Năm' => ['label' => 'Năm', 'value' => '400k'],
                        'Giải Sáu' => ['label' => 'Sáu', 'value' => '200k'],
                    ];
                @endphp
                @foreach($prizes as $prizeName => $numbers)
                    @php
                        $config = $prizeConfig[$prizeName] ?? ['label' => $prizeName, 'value' => '-'];
                    @endphp
                    <tr>
                        <td class="font-semibold whitespace-nowrap">{{ $config['label'] }}</td>
                        <td>
                            <div class="flex flex-wrap justify-center gap-1">
                                @foreach($numbers as $number)
                                    <span class="vietlott-3d-number">
                                        {{ str_pad($number, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td>-</td>
                        <td class="text-[#ff6600] font-bold whitespace-nowrap">{{ $config['value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
