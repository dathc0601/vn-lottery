<div class="top-performers">
    @if ($topNumbers->isEmpty())
        <div class="text-center py-12 text-gray-500">
            <p>No data available. Please select a province.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Top Numbers --}}
            <div class="top-numbers">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span class="text-2xl">🔝</span>
                    Top {{ $limit }} Numbers
                </h3>

                <div class="space-y-3">
                    @foreach ($topNumbers as $index => $number)
                        <div class="performer-card bg-gradient-to-r from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-lg p-4 transition-all hover:shadow-md">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="rank-badge bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="number-display font-mono text-2xl font-bold">
                                        {{ $number['number'] }}
                                    </span>
                                </div>
                                <span class="frequency-badge bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $number['frequency'] }}x
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>
                                    Last: {{ $number['last_appeared'] ? $number['last_appeared']->diffForHumans() : 'Never' }}
                                </span>
                                <span>
                                    Cycle: {{ $number['cycle_count'] }} days
                                </span>
                            </div>

                            {{-- Sparkline --}}
                            <div class="mt-2 sparkline-container" style="height: 30px;">
                                <svg viewBox="0 0 100 30" class="w-full h-full">
                                    @php
                                        $max = max($number['sparkline']);
                                        $points = [];
                                        foreach ($number['sparkline'] as $i => $value) {
                                            $x = ($i / (count($number['sparkline']) - 1)) * 100;
                                            $y = $max > 0 ? 30 - (($value / $max) * 25) : 15;
                                            $points[] = "{$x},{$y}";
                                        }
                                        $pathData = 'M ' . implode(' L ', $points);
                                    @endphp
                                    <path
                                        d="{{ $pathData }}"
                                        fill="none"
                                        stroke="rgb(239, 68, 68)"
                                        stroke-width="2"
                                        class="sparkline-path"
                                    />
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Bottom Numbers --}}
            <div class="bottom-numbers">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <span class="text-2xl">🔻</span>
                    Bottom {{ $limit }} Numbers
                </h3>

                <div class="space-y-3">
                    @foreach ($bottomNumbers as $index => $number)
                        <div class="performer-card bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg p-4 transition-all hover:shadow-md">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <span class="rank-badge bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="number-display font-mono text-2xl font-bold">
                                        {{ $number['number'] }}
                                    </span>
                                </div>
                                <span class="frequency-badge bg-blue-500 text-white px-3 py-1 rounded-full text-sm font-bold">
                                    {{ $number['frequency'] }}x
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                                <span>
                                    Last: {{ $number['last_appeared'] ? $number['last_appeared']->diffForHumans() : 'Never' }}
                                </span>
                                <span>
                                    Cycle: {{ $number['cycle_count'] }} days
                                </span>
                            </div>

                            {{-- Sparkline --}}
                            <div class="mt-2 sparkline-container" style="height: 30px;">
                                <svg viewBox="0 0 100 30" class="w-full h-full">
                                    @php
                                        $max = max($number['sparkline']);
                                        $points = [];
                                        foreach ($number['sparkline'] as $i => $value) {
                                            $x = ($i / (count($number['sparkline']) - 1)) * 100;
                                            $y = $max > 0 ? 30 - (($value / $max) * 25) : 15;
                                            $points[] = "{$x},{$y}";
                                        }
                                        $pathData = 'M ' . implode(' L ', $points);
                                    @endphp
                                    <path
                                        d="{{ $pathData }}"
                                        fill="none"
                                        stroke="rgb(59, 130, 246)"
                                        stroke-width="2"
                                        class="sparkline-path"
                                    />
                                </svg>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
