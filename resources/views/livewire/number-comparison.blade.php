<div class="number-comparison" x-data="{ numberInput: '' }">
    {{-- Number Selection --}}
    <div class="mb-6">
        <div class="flex gap-2 items-center flex-wrap">
            <input
                type="text"
                x-model="numberInput"
                @keydown.enter="
                    if (numberInput.length <= 2 && numberInput >= 0 && numberInput <= 99) {
                        $wire.addNumber(numberInput.padStart(2, '0'));
                        numberInput = '';
                    }
                "
                placeholder="Enter number (00-99)"
                maxlength="2"
                class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white px-4 py-2 w-48"
            />

            <button
                @click="
                    if (numberInput.length <= 2 && numberInput >= 0 && numberInput <= 99) {
                        $wire.addNumber(numberInput.padStart(2, '0'));
                        numberInput = '';
                    }
                "
                class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors"
            >
                + Add Number
            </button>

            <span class="text-sm text-gray-500">
                (Max 4 numbers for comparison)
            </span>
        </div>

        {{-- Selected Numbers --}}
        @if (!empty($compareNumbers))
            <div class="mt-4 flex gap-2 flex-wrap">
                @foreach ($compareNumbers as $number)
                    <span class="inline-flex items-center gap-2 bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200 px-3 py-1 rounded-full">
                        <span class="font-mono font-bold">{{ $number }}</span>
                        <button
                            wire:click="removeNumber('{{ $number }}')"
                            class="hover:text-red-600 transition-colors"
                        >
                            ×
                        </button>
                    </span>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Comparison Grid --}}
    @if (empty($comparisonData))
        <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            <p class="text-lg mb-2">🔍 No numbers to compare</p>
            <p class="text-sm">Add 2-4 numbers above to see their side-by-side comparison.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min(count($comparisonData), 4) }} gap-4">
            @foreach ($comparisonData as $data)
                <div class="comparison-card bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-6 shadow-md">
                    {{-- Number Header --}}
                    <div class="text-center mb-4">
                        <div class="number-display font-mono text-5xl font-bold text-purple-600 dark:text-purple-400 mb-2">
                            {{ $data['number'] }}
                        </div>
                        <div class="status-badge inline-block px-3 py-1 rounded-full text-sm font-bold
                            @if ($data['status'] === 'hot') bg-red-500 text-white
                            @elseif ($data['status'] === 'warm') bg-orange-500 text-white
                            @elseif ($data['status'] === 'normal') bg-green-500 text-white
                            @elseif ($data['status'] === 'cool') bg-blue-500 text-white
                            @else bg-gray-500 text-white
                            @endif
                        ">
                            @if ($data['status'] === 'hot') 🔥 HOT
                            @elseif ($data['status'] === 'warm') ⚡ WARM
                            @elseif ($data['status'] === 'normal') ✓ NORMAL
                            @elseif ($data['status'] === 'cool') ❄️ COOL
                            @else ⚪ COLD
                            @endif
                        </div>
                    </div>

                    {{-- Key Metrics --}}
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Frequency</span>
                            <span class="text-lg font-bold">{{ $data['frequency'] }}x</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Last Appeared</span>
                            <span class="text-sm font-medium">
                                {{ $data['last_appeared'] ? $data['last_appeared']->diffForHumans() : 'Never' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Cycle Count</span>
                            <span class="text-sm font-medium">{{ $data['cycle_count'] }} days</span>
                        </div>
                    </div>

                    {{-- All Frequencies --}}
                    <div class="border-t border-purple-200 dark:border-purple-700 pt-4">
                        <h4 class="text-sm font-bold mb-3 text-gray-700 dark:text-gray-300">All Periods</h4>
                        <div class="space-y-2">
                            @foreach ($data['all_frequencies'] as $period => $freq)
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $period }}</span>
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            @php
                                                $maxPeriodFreq = max($data['all_frequencies']);
                                                $percentage = $maxPeriodFreq > 0 ? ($freq / $maxPeriodFreq) * 100 : 0;
                                            @endphp
                                            <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="font-mono font-bold w-8 text-right">{{ $freq }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
