<div class="sidebar-section">
    <div class="sidebar-header-dark">Lịch mở thưởng hôm nay</div>
    <div class="p-3">
        @php
            $timeSlots = [
                '16:15' => ['label' => "16h15'", 'region' => 'XSMN'],
                '17:15' => ['label' => "17h15'", 'region' => 'XSMT'],
                '18:15' => ['label' => "18h15'", 'region' => 'XSMB'],
            ];
        @endphp

        <div class="grid grid-cols-3 gap-2 text-sm">
            @foreach($timeSlots as $time => $slot)
                <div>
                    <div class="font-bold text-[#8B2500] mb-1 text-center">{{ $slot['label'] }}</div>
                    <div class="text-center">
                        <span class="font-semibold text-xs">{{ $slot['region'] }}</span>
                    </div>
                    @if(isset($todaySchedule[$time]))
                        @foreach($todaySchedule[$time] as $province)
                            <div class="text-xs text-gray-600 text-center mt-0.5">
                                <a href="{{ route('province.detail', ['code' => $province->code, 'slug' => $province->slug]) }}"
                                   class="text-[#0066cc] hover:underline">
                                    {{ $province->name }}
                                </a>
                            </div>
                        @endforeach
                    @endif
                    @if($time === '18:15')
                        <div class="text-xs text-gray-600 text-center mt-0.5">
                            <a href="{{ route('vietlott.mega645') }}" class="text-[#0066cc] hover:underline">Mega 6/45</a>
                        </div>
                        <div class="text-xs text-gray-600 text-center mt-0.5">
                            <a href="{{ route('vietlott.max3d') }}" class="text-[#0066cc] hover:underline">Max 3D</a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-2 text-center">
            <a href="{{ route('schedule') }}" class="text-xs text-[#0066cc] hover:underline">Xem lịch đầy đủ &raquo;</a>
        </div>
    </div>
</div>
