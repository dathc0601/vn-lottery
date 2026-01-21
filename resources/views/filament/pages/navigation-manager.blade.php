<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column: Navigation Items Tree --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('admin.navigation.items') }}
                </h3>
                <div class="flex gap-2">
                    <x-filament::button
                        color="gray"
                        size="sm"
                        wire:click="togglePreview"
                    >
                        <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                        {{ $showPreview ? __('admin.navigation.hide_preview') : __('admin.navigation.show_preview') }}
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        size="sm"
                        wire:click="clearCache"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                        {{ __('admin.navigation.clear_cache') }}
                    </x-filament::button>
                </div>
            </div>

            {{-- Navigation Items List --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                @forelse($items as $item)
                    <div
                        class="border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                        x-data="{ expanded: false }"
                    >
                        {{-- Parent Item --}}
                        <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-1">
                                    <button wire:click="moveUp({{ $item->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <x-heroicon-o-chevron-up class="w-4 h-4" />
                                    </button>
                                    <button wire:click="moveDown({{ $item->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                                    </button>
                                </div>

                                {{-- Expand/collapse toggle (only for items with children) --}}
                                @if($item->allChildren->count() > 0)
                                    <button
                                        @click="expanded = !expanded"
                                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    >
                                        <x-heroicon-o-chevron-right
                                            class="w-5 h-5 transition-transform duration-200"
                                            x-bind:class="{ 'rotate-90': expanded }"
                                        />
                                    </button>
                                @else
                                    <x-heroicon-o-bars-3 class="w-5 h-5 text-gray-400" />
                                @endif

                                {{-- Title (clickable to expand if has children) --}}
                                <div
                                    @if($item->allChildren->count() > 0)
                                        @click="expanded = !expanded"
                                        class="cursor-pointer select-none"
                                    @endif
                                >
                                    <span class="font-medium text-gray-900 dark:text-white {{ !$item->is_active ? 'line-through opacity-50' : '' }}">
                                        {{ $item->title }}
                                    </span>
                                    @if($item->type !== 'route')
                                        <span class="ml-2 text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                            {{ $item->type }}
                                        </span>
                                    @endif
                                    @if($item->allChildren->count() > 0)
                                        <span class="ml-1 text-xs px-1.5 py-0.5 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full">
                                            {{ $item->allChildren->count() }}
                                        </span>
                                    @endif
                                    @if($item->route_name)
                                        <span class="ml-1 text-xs text-gray-500 dark:text-gray-400">
                                            ({{ $item->route_name }})
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Add child button for items that can have children (only route/static_link) --}}
                                @if(in_array($item->type, ['route', 'static_link']))
                                    <button
                                        wire:click="addChildTo({{ $item->id }})"
                                        class="text-gray-400 hover:text-green-600"
                                        title="{{ __('admin.navigation.add_child') }}"
                                    >
                                        <x-heroicon-o-plus-circle class="w-5 h-5" />
                                    </button>
                                @endif
                                <button
                                    wire:click="toggleActive({{ $item->id }})"
                                    class="{{ $item->is_active ? 'text-green-600' : 'text-gray-400' }} hover:text-green-700"
                                    title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}"
                                >
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                </button>
                                <button
                                    wire:click="editItem({{ $item->id }})"
                                    class="text-gray-400 hover:text-blue-600"
                                    title="Edit"
                                >
                                    <x-heroicon-o-pencil class="w-5 h-5" />
                                </button>
                                <button
                                    wire:click="deleteItem({{ $item->id }})"
                                    wire:confirm="{{ __('admin.navigation.confirm_delete') }}"
                                    class="text-gray-400 hover:text-red-600"
                                    title="Delete"
                                >
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        {{-- Child Items (collapsible) --}}
                        @if($item->allChildren->count() > 0)
                            <div
                                x-show="expanded"
                                x-collapse
                                class="bg-gray-50 dark:bg-gray-900/50"
                            >
                                @foreach($item->allChildren as $child)
                                    <div class="flex items-center justify-between px-4 py-2 pl-12 hover:bg-gray-100 dark:hover:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-2">
                                            {{-- Reorder buttons for children --}}
                                            <div class="flex flex-col gap-0.5">
                                                <button
                                                    wire:click="moveUp({{ $child->id }})"
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    title="Move up"
                                                >
                                                    <x-heroicon-o-chevron-up class="w-3 h-3" />
                                                </button>
                                                <button
                                                    wire:click="moveDown({{ $child->id }})"
                                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                                    title="Move down"
                                                >
                                                    <x-heroicon-o-chevron-down class="w-3 h-3" />
                                                </button>
                                            </div>
                                            <x-heroicon-o-minus class="w-4 h-4 text-gray-300" />
                                            <span class="text-sm text-gray-700 dark:text-gray-300 {{ !$child->is_active ? 'line-through opacity-50' : '' }}">
                                                {{ $child->title }}
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button
                                                wire:click="toggleActive({{ $child->id }})"
                                                class="{{ $child->is_active ? 'text-green-600' : 'text-gray-400' }} hover:text-green-700"
                                            >
                                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                            </button>
                                            <button
                                                wire:click="editItem({{ $child->id }})"
                                                class="text-gray-400 hover:text-blue-600"
                                            >
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                            </button>
                                            <button
                                                wire:click="deleteItem({{ $child->id }})"
                                                wire:confirm="{{ __('admin.navigation.confirm_delete') }}"
                                                class="text-gray-400 hover:text-red-600"
                                            >
                                                <x-heroicon-o-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                        {{ __('admin.navigation.no_items') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column: Form + Preview --}}
        <div class="space-y-4">
            {{-- Edit/Add Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $editingItemId ? __('admin.navigation.edit_item') : __('admin.navigation.add_item') }}
                </h3>

                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="mt-6 flex gap-3">
                        <x-filament::button type="submit">
                            {{ $editingItemId ? __('admin.common.save') : __('admin.common.create') }}
                        </x-filament::button>
                        @if($editingItemId)
                            <x-filament::button
                                type="button"
                                color="gray"
                                wire:click="cancelEdit"
                            >
                                {{ __('admin.common.cancel') }}
                            </x-filament::button>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Preview --}}
            @if($showPreview)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('admin.navigation.preview') }}
                    </h3>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <x-navigation.main-nav />
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
