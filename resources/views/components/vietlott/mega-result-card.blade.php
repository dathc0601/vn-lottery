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
@endphp

<div class="rounded-lg overflow-hidden border border-gray-200 shadow-sm">
    <!-- Branded Header -->
    <div class="bg-[#eb1427] px-4 py-3">
        <img class="w-1/2 h-2/3 mx-auto" src="{{ asset('images/vietlott-mega-645-logo-white.png') }}"  alt="Mega 6/45"/>
    </div>

    <!-- Content -->
    <div class="bg-white p-4">
        <!-- Title Link -->
        <a href="{{ route('vietlott.mega645') }}" class="text-blue-700 font-semibold underline hover:text-blue-900 text-base">
            Kết quả xổ số Mega 6/45
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
            @foreach($result->winning_numbers as $number)
                <div class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold text-base shadow" style="background: radial-gradient(circle at 50% 20%, #fe7e8a, red)">
                    {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
                </div>
            @endforeach
        </div>
    </div>
</div>
