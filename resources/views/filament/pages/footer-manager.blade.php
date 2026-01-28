<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Left Column: Footer Columns & Links Tree --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ __('admin.footer_manager.columns') }}
                </h3>
                <div class="flex gap-2">
                    <x-filament::button
                        color="gray"
                        size="sm"
                        wire:click="togglePreview"
                    >
                        <x-heroicon-o-eye class="w-4 h-4 mr-1" />
                        {{ $showPreview ? __('admin.footer_manager.hide_preview') : __('admin.footer_manager.show_preview') }}
                    </x-filament::button>
                    <x-filament::button
                        color="gray"
                        size="sm"
                        wire:click="clearCache"
                    >
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-1" />
                        {{ __('admin.footer_manager.clear_cache') }}
                    </x-filament::button>
                </div>
            </div>

            {{-- Columns List --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                @forelse($columns as $column)
                    <div
                        class="border-b border-gray-200 dark:border-gray-700 last:border-b-0"
                        x-data="{ expanded: true }"
                    >
                        {{-- Column Header --}}
                        <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-1">
                                    <button wire:click="moveColumnUp({{ $column->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <x-heroicon-o-chevron-up class="w-4 h-4" />
                                    </button>
                                    <button wire:click="moveColumnDown({{ $column->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <x-heroicon-o-chevron-down class="w-4 h-4" />
                                    </button>
                                </div>

                                @if($column->type === 'links' && $column->links->count() > 0)
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
                                    <x-heroicon-o-squares-2x2 class="w-5 h-5 text-gray-400" />
                                @endif

                                <div
                                    @if($column->type === 'links' && $column->links->count() > 0)
                                        @click="expanded = !expanded"
                                        class="cursor-pointer select-none"
                                    @endif
                                >
                                    <span class="font-medium text-gray-900 dark:text-white {{ !$column->is_active ? 'line-through opacity-50' : '' }}">
                                        {{ $column->title }}
                                    </span>
                                    <span class="ml-2 text-xs px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                        {{ $column->type }}
                                    </span>
                                    @if($column->type === 'links' && $column->links->count() > 0)
                                        <span class="ml-1 text-xs px-1.5 py-0.5 bg-primary-100 dark:bg-primary-900 text-primary-700 dark:text-primary-300 rounded-full">
                                            {{ $column->links->count() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if($column->type === 'links')
                                    <button
                                        wire:click="addLinkToColumn({{ $column->id }})"
                                        class="text-gray-400 hover:text-green-600"
                                        title="{{ __('admin.footer_manager.add_link') }}"
                                    >
                                        <x-heroicon-o-plus-circle class="w-5 h-5" />
                                    </button>
                                @endif
                                <button
                                    wire:click="toggleColumnActive({{ $column->id }})"
                                    class="{{ $column->is_active ? 'text-green-600' : 'text-gray-400' }} hover:text-green-700"
                                    title="{{ $column->is_active ? 'Deactivate' : 'Activate' }}"
                                >
                                    <x-heroicon-o-check-circle class="w-5 h-5" />
                                </button>
                                <button
                                    wire:click="editColumn({{ $column->id }})"
                                    class="text-gray-400 hover:text-blue-600"
                                    title="Edit"
                                >
                                    <x-heroicon-o-pencil class="w-5 h-5" />
                                </button>
                                <button
                                    wire:click="deleteColumn({{ $column->id }})"
                                    wire:confirm="{{ __('admin.footer_manager.confirm_delete') }}"
                                    class="text-gray-400 hover:text-red-600"
                                    title="Delete"
                                >
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>

                        {{-- Links within column --}}
                        @if($column->type === 'links' && $column->links->count() > 0)
                            <div
                                x-show="expanded"
                                x-collapse
                                class="bg-gray-50 dark:bg-gray-900/50"
                            >
                                @foreach($column->links as $link)
                                    <div class="flex items-center justify-between px-4 py-2 pl-12 hover:bg-gray-100 dark:hover:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center gap-2">
                                            <div class="flex flex-col gap-0.5">
                                                <button wire:click="moveLinkUp({{ $link->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <x-heroicon-o-chevron-up class="w-3 h-3" />
                                                </button>
                                                <button wire:click="moveLinkDown({{ $link->id }})" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                                    <x-heroicon-o-chevron-down class="w-3 h-3" />
                                                </button>
                                            </div>
                                            <x-heroicon-o-link class="w-4 h-4 text-gray-300" />
                                            <span class="text-sm text-gray-700 dark:text-gray-300 {{ !$link->is_active ? 'line-through opacity-50' : '' }}">
                                                {{ $link->label }}
                                            </span>
                                            @if($link->route_name)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $link->route_name }})</span>
                                            @elseif($link->url)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ Str::limit($link->url, 30) }})</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <button
                                                wire:click="toggleLinkActive({{ $link->id }})"
                                                class="{{ $link->is_active ? 'text-green-600' : 'text-gray-400' }} hover:text-green-700"
                                            >
                                                <x-heroicon-o-check-circle class="w-4 h-4" />
                                            </button>
                                            <button
                                                wire:click="editLink({{ $link->id }})"
                                                class="text-gray-400 hover:text-blue-600"
                                            >
                                                <x-heroicon-o-pencil class="w-4 h-4" />
                                            </button>
                                            <button
                                                wire:click="deleteLink({{ $link->id }})"
                                                wire:confirm="{{ __('admin.footer_manager.confirm_delete') }}"
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
                        {{ __('admin.footer_manager.no_columns') }}
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Right Column: Form + Text Settings + Preview --}}
        <div class="space-y-4">
            {{-- Add/Edit Form --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    @if($formMode === 'column')
                        {{ $editingColumnId ? __('admin.footer_manager.edit_column') : __('admin.footer_manager.add_column') }}
                    @else
                        {{ $editingLinkId ? __('admin.footer_manager.edit_link') : __('admin.footer_manager.add_link') }}
                    @endif
                </h3>

                <form wire:submit="save">
                    {{ $this->form }}

                    <div class="mt-6 flex gap-3">
                        <x-filament::button type="submit">
                            {{ ($editingColumnId || $editingLinkId) ? __('admin.common.save') : __('admin.common.create') }}
                        </x-filament::button>
                        @if($editingColumnId || $editingLinkId || $addingLinkToColumnId)
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

            {{-- Text Settings --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ __('admin.footer_manager.text_settings') }}
                </h3>

                <form wire:submit="saveTextSettings">
                    <div class="space-y-4">
                        <div>
                            <label for="footer_about" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('admin.footer_manager.fields.about_text') }}
                            </label>
                            <textarea
                                id="footer_about"
                                wire:model="textData.footer__about_text"
                                rows="3"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            ></textarea>
                        </div>

                        <div>
                            <label for="footer_copyright" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('admin.footer_manager.fields.copyright_template') }}
                            </label>
                            <input
                                type="text"
                                id="footer_copyright"
                                wire:model="textData.footer__copyright_template"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('admin.footer_manager.fields.copyright_template_help') }}
                            </p>
                        </div>

                        <div>
                            <label for="footer_disclaimer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('admin.footer_manager.fields.disclaimer_text') }}
                            </label>
                            <textarea
                                id="footer_disclaimer"
                                wire:model="textData.footer__disclaimer_text"
                                rows="3"
                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            ></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <x-filament::button type="submit">
                            {{ __('admin.common.save') }}
                        </x-filament::button>
                    </div>
                </form>
            </div>

            {{-- Preview --}}
            @if($showPreview)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        {{ __('admin.footer_manager.preview') }}
                    </h3>
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                        <div class="bg-[#333333] text-white p-6">
                            <div class="grid md:grid-cols-3 gap-6">
                                @php
                                    $seo = app(\App\Services\SiteSettingsService::class);
                                    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
                                    $tagline = $seo->get('general', 'tagline', 'Số chuẩn xác - May mắn phát');
                                    $previewColumns = \App\Models\FooterColumn::where('is_active', true)
                                        ->with(['activeLinks'])
                                        ->orderBy('sort_order')
                                        ->get();
                                @endphp
                                @foreach($previewColumns as $col)
                                    <div>
                                        @if($col->type === 'about')
                                            <h3 class="font-bold text-lg mb-3">{{ $siteName }}</h3>
                                            <p class="text-sm text-gray-300">{{ $seo->get('footer', 'about_text', 'Trang web cung cấp kết quả xổ số 3 miền nhanh nhất và chính xác nhất') }}</p>
                                        @elseif($col->type === 'links')
                                            <h3 class="font-bold text-lg mb-3">{{ $col->title }}</h3>
                                            <ul class="space-y-2 text-sm">
                                                @foreach($col->activeLinks as $link)
                                                    <li>
                                                        <span class="text-gray-300 hover:text-[#ff6600]">{{ $link->label }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @elseif($col->type === 'info')
                                            <h3 class="font-bold text-lg mb-3">{{ $col->title }}</h3>
                                            <p class="text-sm text-gray-300">{{ $seo->copyrightText() }}</p>
                                            <p class="text-xs text-gray-400 mt-2">{{ $seo->get('footer', 'disclaimer_text', 'Kết quả chỉ mang tính chất tham khảo') }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $tagline }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Extended Footer Settings --}}
    <div class="mt-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                {{ __('admin.footer_manager.extended_settings') }}
            </h3>

            <form wire:submit="saveExtendedSettings">
                {{ $this->extendedForm }}

                <div class="mt-6">
                    <x-filament::button type="submit">
                        {{ __('admin.common.save') }}
                    </x-filament::button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
