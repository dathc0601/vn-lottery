<x-filament-panels::page>
    @vite(['resources/css/number-analytics.css', 'resources/js/number-analytics.js'])

    <div class="analytics-dashboard">
        {{-- Filter Bar --}}
        <div class="mb-6">
            <x-filament::section>
                <x-slot name="heading">
                    Filters & Configuration
                </x-slot>

                {{ $this->form }}
            </x-filament::section>
        </div>

        {{-- Key Metrics will be rendered via widgets --}}
        <div class="mb-6" wire:key="widgets-section">
            @foreach ($this->getHeaderWidgets() as $widgetIndex => $widget)
                @livewire($widget, ['lazy' => true], key('widget-' . $widgetIndex . '-' . $this->selectedProvinceId . '-' . $this->selectedPeriod))
            @endforeach
        </div>

        {{-- Heat Map Section --}}
        <div class="mb-6" wire:key="heatmap-section">
            <x-filament::section>
                <x-slot name="heading">
                    🎯 Number Heat Map
                </x-slot>

                <x-slot name="description">
                    Visual representation of number frequencies. Darker colors indicate higher frequency.
                </x-slot>

                <livewire:number-heat-map
                    :province-id="$this->selectedProvinceId"
                    :period="$this->selectedPeriod"
                    wire:key="heatmap-{{ $this->selectedProvinceId }}-{{ $this->selectedPeriod }}"
                />
            </x-filament::section>
        </div>

        {{-- Top & Bottom Performers --}}
        <div class="mb-6" wire:key="performers-section">
            <x-filament::section>
                <x-slot name="heading">
                    📊 Top & Bottom Performers
                </x-slot>

                <x-slot name="description">
                    Numbers with highest and lowest frequencies in the selected period.
                </x-slot>

                <livewire:top-performers
                    :province-id="$this->selectedProvinceId"
                    :period="$this->selectedPeriod"
                    :limit="10"
                    wire:key="performers-{{ $this->selectedProvinceId }}-{{ $this->selectedPeriod }}"
                />
            </x-filament::section>
        </div>

        {{-- Trend Chart --}}
        <div class="mb-6" wire:key="trend-section">
            <x-filament::section>
                <x-slot name="heading">
                    📈 Frequency Trend Analysis
                </x-slot>

                <x-slot name="description">
                    Compare frequency evolution across different time periods for selected numbers.
                </x-slot>

                <livewire:number-trend-chart
                    :province-id="$this->selectedProvinceId"
                    :selected-numbers="[]"
                    wire:key="trend-{{ $this->selectedProvinceId }}"
                />
            </x-filament::section>
        </div>

        {{-- Number Comparison --}}
        <div class="mb-6" wire:key="comparison-section">
            <x-filament::section>
                <x-slot name="heading">
                    🔍 Number Comparison Tool
                </x-slot>

                <x-slot name="description">
                    Compare multiple numbers side-by-side to analyze their performance metrics.
                </x-slot>

                <livewire:number-comparison
                    :province-id="$this->selectedProvinceId"
                    :period="$this->selectedPeriod"
                    :compare-numbers="[]"
                    wire:key="comparison-{{ $this->selectedProvinceId }}-{{ $this->selectedPeriod }}"
                />
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
