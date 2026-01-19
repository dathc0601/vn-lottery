<div class="number-trend-chart" x-data="{ numberInput: '' }">
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
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
            >
                + Add Number
            </button>

            <span class="text-sm text-gray-500">
                (Max 5 numbers)
            </span>
        </div>

        {{-- Selected Numbers --}}
        @if (!empty($selectedNumbers))
            <div class="mt-4 flex gap-2 flex-wrap">
                @foreach ($selectedNumbers as $number)
                    <span class="inline-flex items-center gap-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 px-3 py-1 rounded-full">
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

    {{-- Chart Area --}}
    @if (empty($chartData))
        <div class="text-center py-12 text-gray-500 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
            <p class="text-lg mb-2">📊 No numbers selected</p>
            <p class="text-sm">Add numbers above to see their frequency trends across different time periods.</p>
        </div>
    @else
        <div class="chart-container bg-white dark:bg-gray-900 rounded-lg p-6 shadow-sm">
            <canvas
                id="trendChart"
                x-data="{
                    chart: null,
                    initChart() {
                        const ctx = document.getElementById('trendChart');
                        if (!ctx) return;

                        const chartData = {{ Js::from($chartData) }};

                        const datasets = chartData.map(item => ({
                            label: 'Number ' + item.number,
                            data: item.data,
                            borderColor: item.color,
                            backgroundColor: item.color + '20',
                            tension: 0.4,
                            fill: true,
                        }));

                        if (this.chart) {
                            this.chart.destroy();
                        }

                        this.chart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['30d', '60d', '90d', '100d', '200d', '300d', '500d'],
                                datasets: datasets
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                aspectRatio: 2,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Frequency Evolution Across Time Periods'
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Frequency'
                                        }
                                    },
                                    x: {
                                        title: {
                                            display: true,
                                            text: 'Time Period'
                                        }
                                    }
                                },
                                interaction: {
                                    mode: 'nearest',
                                    axis: 'x',
                                    intersect: false
                                }
                            }
                        });
                    }
                }"
                x-init="
                    if (typeof Chart === 'undefined') {
                        const script = document.createElement('script');
                        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                        script.onload = () => initChart();
                        document.head.appendChild(script);
                    } else {
                        initChart();
                    }
                "
                wire:key="chart-{{ implode('-', $selectedNumbers) }}"
            ></canvas>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('number-added', () => {
        // Re-render chart when number is added
        setTimeout(() => {
            Alpine.nextTick(() => {
                const canvas = document.getElementById('trendChart');
                if (canvas && canvas.__x) {
                    canvas.__x.$data.initChart();
                }
            });
        }, 100);
    });
</script>
@endscript
