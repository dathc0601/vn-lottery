<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            {{ __('widget.quick_actions.title') }}
        </x-slot>

        <x-slot name="description">
            {{ __('widget.quick_actions.description') }}
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::button
                wire:click="fetchAllProvinces"
                icon="heroicon-o-arrow-path"
                color="primary"
                size="lg"
            >
                {{ __('widget.quick_actions.fetch_all') }}
            </x-filament::button>

            <x-filament::button
                wire:click="generateStatistics"
                icon="heroicon-o-chart-bar"
                color="success"
                size="lg"
            >
                {{ __('widget.quick_actions.generate_stats') }}
            </x-filament::button>

            <x-filament::button
                wire:click="clearOldLogs"
                icon="heroicon-o-trash"
                color="danger"
                size="lg"
            >
                {{ __('widget.quick_actions.clear_logs') }}
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
