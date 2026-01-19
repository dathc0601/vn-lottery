<div class="number-heat-map">
    @if (empty($heatMapData))
        <div class="text-center py-12 text-gray-500">
            <p>No data available. Please select a province.</p>
        </div>
    @else
        {{-- Heat Map Grid --}}
        <div class="heat-map-grid grid grid-cols-10 gap-2 mb-6">
            @foreach ($heatMapData as $cell)
                <div
                    class="heat-cell heat-{{ $cell['color'] }} rounded-lg p-4 text-center cursor-pointer transition-all duration-300 hover:scale-110 hover:z-10 hover:shadow-lg"
                    wire:click="selectNumber('{{ $cell['number'] }}')"
                    x-data="{ showTooltip: false }"
                    @mouseenter="showTooltip = true"
                    @mouseleave="showTooltip = false"
                >
                    <div class="number-text font-mono text-lg font-bold">{{ $cell['number'] }}</div>
                    <div class="frequency-text text-xs opacity-75 mt-1">{{ $cell['frequency'] }}</div>

                    {{-- Tooltip --}}
                    <div
                        x-show="showTooltip"
                        x-transition
                        class="absolute z-50 p-3 bg-gray-900 text-white text-sm rounded-lg shadow-xl pointer-events-none"
                        style="margin-top: -100px; margin-left: -50px;"
                    >
                        <div class="font-bold mb-1">Number {{ $cell['number'] }}</div>
                        <div>Frequency: {{ $cell['frequency'] }}</div>
                        @if ($cell['last_appeared'])
                            <div>Last: {{ $cell['last_appeared']->diffForHumans() }}</div>
                        @else
                            <div>Last: Never</div>
                        @endif
                        <div>Cycle: {{ $cell['cycle_count'] }} days</div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Legend --}}
        <div class="heat-map-legend flex items-center justify-center gap-6 py-4">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded heat-cold"></div>
                <span class="text-sm">Cold</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded heat-cool"></div>
                <span class="text-sm">Cool</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded heat-normal"></div>
                <span class="text-sm">Normal</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded heat-warm"></div>
                <span class="text-sm">Warm</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded heat-hot"></div>
                <span class="text-sm">Hot</span>
            </div>
        </div>

        {{-- Selected Number Details --}}
        @if ($selectedNumber)
            <div class="mt-6 p-4 bg-blue-50 dark:bg-gray-800 rounded-lg">
                <h3 class="text-lg font-bold mb-2">Selected Number: {{ $selectedNumber }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Click on the heat map to view detailed statistics for each number.
                </p>
            </div>
        @endif
    @endif
</div>
