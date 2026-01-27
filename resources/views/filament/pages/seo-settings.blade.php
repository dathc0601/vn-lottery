<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit">
                {{ __('admin.common.save') }}
            </x-filament::button>

            <x-filament::button color="gray" wire:click="clearCache" type="button">
                {{ __('admin.seo_settings.clear_cache') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
