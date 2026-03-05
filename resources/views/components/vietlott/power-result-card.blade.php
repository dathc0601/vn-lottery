@props(['result'])

@php
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

<div class="rounded-lg overflow-hidden border border-gray-200 shadow-sm">
    <!-- Branded Header -->
    <div class="bg-[#cd3c0e] px-4 py-3">
        <img class="w-1/2 h-2/3 mx-auto" src="{{ asset('images/vietlott-mega-645-logo-white.png') }}"  alt="Power 6/45"/>
    </div>

    <!-- Content -->
    <div class="bg-white p-4">
        <!-- Title Link -->
        <a href="{{ route('vietlott.power655') }}" class="text-blue-700 font-semibold underline hover:text-blue-900 text-base">
            Kết quả xổ số Power 6/55
        </a>

        <!-- Draw Info -->
        <div class="text-gray-500 text-sm mt-1">
            Kỳ quay: #{{ $result->draw_number }} - {{ $dayName }}, {{ $drawDate->format('d/m/Y') }}
        </div>

        <!-- Jackpot Amount -->
        @if($result->jackpot_amount > 0)
            <div class="text-center my-4">
                <span class="text-2xl font-bold text-gray-800">{{ number_format($result->jackpot_amount, 0, ',', '.') }} đ</span>
            </div>
        @endif

        <!-- Winning Numbers -->
        <div class="flex flex-wrap justify-center gap-2 mt-3">
            @foreach($mainNumbers as $number)
                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-base shadow" style="background: radial-gradient(circle at 50% 25%, #fbe6c7, #ff9800)">
                    {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
                </div>
            @endforeach
            @if($specialNumber)
                <div class="w-10 h-10 rounded-full bg-[#2E7D32] text-white flex items-center justify-center font-bold text-base shadow">
                    {{ str_pad($specialNumber, 2, '0', STR_PAD_LEFT) }}
                </div>
            @endif
        </div>
    </div>
</div>
