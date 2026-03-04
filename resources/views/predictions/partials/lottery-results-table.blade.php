@php
    $dateFormatted = $referenceDate instanceof \Carbon\Carbon
        ? $referenceDate->format('d/m/Y')
        : date('d/m/Y', strtotime($referenceDate));

    // Get the first result (for XSMB there's only one province)
    $result = is_array($results) ? ($results[0] ?? null) : null;

    // Define prize structure based on region with column counts for grid
    if ($regionSlug === 'xsmb') {
        $prizes = [
            ['key' => 'prize_special', 'label' => 'G.ĐB', 'cols' => 1, 'highlight' => true],
            ['key' => 'prize_1', 'label' => 'G.1', 'cols' => 1, 'highlight' => false],
            ['key' => 'prize_2', 'label' => 'G.2', 'cols' => 2, 'highlight' => false],
            ['key' => 'prize_3', 'label' => 'G.3', 'cols' => 3, 'highlight' => false],
            ['key' => 'prize_4', 'label' => 'G.4', 'cols' => 4, 'highlight' => false],
            ['key' => 'prize_5', 'label' => 'G.5', 'cols' => 3, 'highlight' => false],
            ['key' => 'prize_6', 'label' => 'G.6', 'cols' => 3, 'highlight' => false],
            ['key' => 'prize_7', 'label' => 'G.7', 'cols' => 4, 'highlight' => false],
        ];
    } else {
        $prizes = [
            ['key' => 'prize_special', 'label' => 'G.ĐB', 'cols' => 1, 'highlight' => true],
            ['key' => 'prize_1', 'label' => 'G.1', 'cols' => 1, 'highlight' => false],
            ['key' => 'prize_2', 'label' => 'G.2', 'cols' => 1, 'highlight' => false],
            ['key' => 'prize_3', 'label' => 'G.3', 'cols' => 2, 'highlight' => false],
            ['key' => 'prize_4', 'label' => 'G.4', 'cols' => 1, 'highlight' => false],
            ['key' => 'prize_5', 'label' => 'G.5', 'cols' => 1, 'highlight' => false],
            ['key' => 'prize_6', 'label' => 'G.6', 'cols' => 3, 'highlight' => false],
            ['key' => 'prize_7', 'label' => 'G.7', 'cols' => 1, 'highlight' => false],
            ['key' => 'prize_8', 'label' => 'G.8', 'cols' => 1, 'highlight' => false],
        ];
    }
@endphp

@if($result)
<div class="my-6">
    <div class="border border-gray-300 overflow-hidden">
        {{-- Table header --}}
        <div class="grid grid-cols-[80px_1fr] bg-[#8B2500] text-white text-sm font-bold">
            <div class="px-3 py-2 text-center border-r border-[#a03000]">Giải</div>
            <div class="px-3 py-2 text-center">Xổ số Miền Bắc ngày {{ $dateFormatted }}</div>
        </div>

        {{-- Prize rows --}}
        @foreach($prizes as $prize)
            @php
                $prizeValue = $result[$prize['key']] ?? '-';
                $values = is_string($prizeValue) ? explode(',', $prizeValue) : [$prizeValue];
                $values = array_map('trim', $values);
                $cols = $prize['cols'];
            @endphp
            <div class="grid grid-cols-[80px_1fr] border-b border-gray-200 last:border-b-0">
                {{-- Label cell --}}
                <div class="px-3 py-2 bg-gray-50 text-sm font-semibold text-gray-700 text-center border-r border-gray-200 flex items-center justify-center">
                    {{ $prize['label'] }}
                </div>
                {{-- Values cell --}}
                <div class="px-2 py-2">
                    @if($prize['highlight'])
                        {{-- Special prize: large red bold --}}
                        <div class="text-center text-2xl font-bold text-red-600">
                            {{ $values[0] ?? '-' }}
                        </div>
                    @elseif($prize['key'] === 'prize_7')
                        {{-- G.7: highlight last 2 digits in red --}}
                        <div class="grid gap-1 text-center text-base font-medium text-gray-800" style="grid-template-columns: repeat({{ $cols }}, 1fr)">
                            @foreach($values as $v)
                                <div>
                                    @php
                                        $v = trim($v);
                                        $prefix = strlen($v) > 2 ? substr($v, 0, -2) : '';
                                        $suffix = substr($v, -2);
                                    @endphp
                                    {{ $prefix }}<span class="font-bold text-red-600">{{ $suffix }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Normal prizes: grid layout --}}
                        <div class="grid gap-1 text-center text-base font-medium text-gray-800" style="grid-template-columns: repeat({{ $cols }}, 1fr)">
                            @foreach($values as $v)
                                <div>{{ trim($v) }}</div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
