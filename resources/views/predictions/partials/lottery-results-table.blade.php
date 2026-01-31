@php
    $dateFormatted = $referenceDate instanceof \Carbon\Carbon
        ? $referenceDate->format('d/m/Y')
        : date('d/m/Y', strtotime($referenceDate));

    // Get the first result (for XSMB there's only one province)
    $result = is_array($results) ? ($results[0] ?? null) : null;

    // Define prize structure based on region
    if ($regionSlug === 'xsmb') {
        $prizes = [
            ['key' => 'prize_special', 'label' => 'ĐB', 'highlight' => true],
            ['key' => 'prize_1', 'label' => 'G1', 'highlight' => false],
            ['key' => 'prize_2', 'label' => 'G2', 'highlight' => false],
            ['key' => 'prize_3', 'label' => 'G3', 'highlight' => false],
            ['key' => 'prize_4', 'label' => 'G4', 'highlight' => false],
            ['key' => 'prize_5', 'label' => 'G5', 'highlight' => false],
            ['key' => 'prize_6', 'label' => 'G6', 'highlight' => false],
            ['key' => 'prize_7', 'label' => 'G7', 'highlight' => false],
        ];
    } else {
        $prizes = [
            ['key' => 'prize_special', 'label' => 'ĐB', 'highlight' => true],
            ['key' => 'prize_1', 'label' => 'G1', 'highlight' => false],
            ['key' => 'prize_2', 'label' => 'G2', 'highlight' => false],
            ['key' => 'prize_3', 'label' => 'G3', 'highlight' => false],
            ['key' => 'prize_4', 'label' => 'G4', 'highlight' => false],
            ['key' => 'prize_5', 'label' => 'G5', 'highlight' => false],
            ['key' => 'prize_6', 'label' => 'G6', 'highlight' => false],
            ['key' => 'prize_7', 'label' => 'G7', 'highlight' => false],
            ['key' => 'prize_8', 'label' => 'G8', 'highlight' => false],
        ];
    }
@endphp

@if($result)
<div class="my-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        Kết quả {{ strtoupper($regionSlug) }} ngày {{ $dateFormatted }}
    </h2>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full border-collapse">
            <tbody>
                @foreach($prizes as $prize)
                    @php
                        $prizeValue = $result[$prize['key']] ?? '-';
                        // Replace comma with " - " for display
                        $displayValue = is_string($prizeValue) ? str_replace(',', ' - ', $prizeValue) : $prizeValue;
                    @endphp
                    <tr class="border-b border-gray-200 last:border-b-0">
                        <td class="border-r border-gray-200 px-4 py-3 w-20 bg-gray-50 text-sm font-semibold text-gray-700">
                            {{ $prize['label'] }}
                        </td>
                        <td class="px-4 py-3 text-center {{ $prize['highlight'] ? 'text-2xl font-bold text-red-600' : 'text-base font-medium text-gray-800' }}">
                            {{ $displayValue }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
