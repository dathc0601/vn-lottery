@php
    $footerService = app(\App\Services\FooterService::class);
    $schedule = $footerService->getScheduleData();
    $dayNames = $schedule['dayNames'] ?? [];
    $rows = $schedule['rows'] ?? [];
    $regionRoutes = [
        'central' => 'xsmt',
        'south' => 'xsmn',
        'north' => 'xsmb',
    ];
@endphp

@if(count($rows) > 0)
<div class="overflow-x-auto footer-schedule-wrapper">
    <table class="w-full border-collapse text-sm footer-schedule-table">
        <thead>
            <tr class="bg-[#ff6600] text-white">
                <th class="border border-[#e55a00] px-3 py-2 text-left font-semibold whitespace-nowrap">Vùng miền</th>
                @foreach($dayNames as $dayNum => $dayLabel)
                    <th class="border border-[#e55a00] px-3 py-2 text-center font-semibold">{{ $dayLabel }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr class="border-b border-gray-200 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                    <td class="border border-gray-200 px-3 py-2 font-semibold text-[#ff6600] whitespace-nowrap">
                        @php $regionRoute = $regionRoutes[$row['region']] ?? null; @endphp
                        @if($regionRoute)
                            <a href="{{ route($regionRoute) }}" class="hover:underline">{{ $row['label'] }}</a>
                        @else
                            {{ $row['label'] }}
                        @endif
                    </td>
                    @foreach($dayNames as $dayNum => $dayLabel)
                        <td class="border border-gray-200 px-2 py-2 text-center text-xs leading-relaxed">
                            @foreach($row['days'][$dayNum] ?? [] as $province)
                                @if(!$loop->first), @endif
                                <a href="{{ url('/xo-so-' . $province['slug']) }}"
                                   class="text-[#0066cc] hover:text-[#ff6600] hover:underline">{{ $province['name'] }}</a>
                            @endforeach
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
