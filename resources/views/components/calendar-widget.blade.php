@props([
    'region' => 'xsmb',  // Default to North region
])

@php
    $currentMonth = request('month', now()->month);
    $currentYear = request('year', now()->year);
    $today = now();

    // Create Carbon instance for the selected month
    $date = \Carbon\Carbon::create($currentYear, $currentMonth, 1);
    $daysInMonth = $date->daysInMonth;
    $firstDayOfWeek = $date->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.

    // Adjust so Monday is first day (Vietnamese standard)
    $startOffset = $firstDayOfWeek == 0 ? 6 : $firstDayOfWeek - 1;

    // Get draw days (for red dots) - simplified, you can make this dynamic
    $drawDays = []; // This could be populated from database

    $weekDays = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
@endphp

<div class="bg-white border border-gray-300 mb-4">
    <!-- Calendar Header -->
    <div class="bg-gray-100 border-b border-gray-300 px-4 py-2 flex items-center justify-between">
        <a href="{{ route($region) }}?month={{ $date->copy()->subMonth()->month }}&year={{ $date->copy()->subMonth()->year }}"
           class="text-gray-600 hover:text-orange-500 font-bold text-lg">&lt;</a>
        <h3 class="font-bold text-gray-800 text-sm">
            Tháng {{ $currentMonth }}/{{ $currentYear }}
        </h3>
        <a href="{{ route($region) }}?month={{ $date->copy()->addMonth()->month }}&year={{ $date->copy()->addMonth()->year }}"
           class="text-gray-600 hover:text-orange-500 font-bold text-lg">&gt;</a>
    </div>

    <!-- Calendar Grid -->
    <div class="p-3">
        <!-- Week Day Headers -->
        <div class="grid grid-cols-7 gap-1 mb-2">
            @foreach($weekDays as $day)
                <div class="text-center text-xs font-semibold text-gray-700 py-1">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        <!-- Calendar Days -->
        <div class="grid grid-cols-7 gap-1">
            @for($i = 0; $i < $startOffset; $i++)
                <div class="text-center py-1.5"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $currentDate = \Carbon\Carbon::create($currentYear, $currentMonth, $day);
                    $isToday = $currentDate->isSameDay($today);
                    $hasDrawToday = in_array($day, $drawDays);
                @endphp

                <div class="text-center relative">
                    <a href="{{ route($region, $currentDate->format('d-m-Y')) }}"
                       class="block py-1.5 text-sm {{ $isToday ? 'bg-orange-500 text-white font-bold' : 'text-gray-700 hover:bg-gray-100' }} transition-colors">
                        {{ $day }}
                    </a>
                    @if($hasDrawToday)
                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2">
                            <span class="inline-block w-1 h-1 bg-red-600 rounded-full"></span>
                        </div>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</div>
