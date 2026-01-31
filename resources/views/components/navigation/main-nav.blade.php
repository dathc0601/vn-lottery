@inject('navService', 'App\Services\NavigationService')

@php
    $items = $navService->getNavigation();
    $seo = app(\App\Services\SiteSettingsService::class);
    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
    $logoUrl = $seo->imageUrl('general', 'site_logo');
@endphp

<nav class="bg-[#ff6600]" x-data="navOverflow()">
    <div class="container mx-auto px-4" style="max-width: 1040px;">
        <ul class="flex items-center h-10" x-ref="navList">
            {{-- Logo (always visible) --}}
            <li class="mr-2 shrink-0" data-logo>
                <a href="{{ route('home') }}" class="flex items-center h-10">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-7">
                    @else
                        <span class="font-bold text-white text-lg">{{ $siteName }}</span>
                    @endif
                </a>
            </li>
            {{-- Divider (always visible) --}}
            <li class="border-l border-white/30 h-5 mx-1 shrink-0" data-divider></li>

            {{-- Nav items with visibility control --}}
            @foreach($items as $index => $item)
                @if($item->type === 'divider')
                    <li class="border-l border-white/30 h-5 mx-1 shrink-0"
                        data-nav-item="{{ $index }}"
                        x-show="isVisible({{ $index }})"
                        x-cloak></li>
                @elseif($item->isSpecialType())
                    <x-navigation.special-item :item="$item" :index="$index" />
                @elseif($item->hasDropdown())
                    <x-navigation.nav-dropdown :item="$item" :index="$index" />
                @else
                    <x-navigation.nav-item :item="$item" :index="$index" />
                @endif
            @endforeach

            {{-- "Xem thêm" dropdown (only shows when items overflow) --}}
            <li class="relative shrink-0"
                x-show="hasOverflow"
                x-data="{ open: false }"
                @mouseenter="open = true"
                @mouseleave="open = false"
                x-cloak>
                <a href="javascript:void(0)"
                   class="flex items-center px-4 text-white font-medium transition-colors duration-200 text-sm cursor-pointer"
                   style="line-height: 40px;"
                   :style="open ? 'background: rgba(0,0,0,0.15)' : ''">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </a>
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 transform -translate-y-1"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 transform translate-y-0"
                     x-transition:leave-end="opacity-0 transform -translate-y-1"
                     class="absolute right-0 top-full bg-white shadow-lg rounded-b-md z-50 min-w-[180px] py-1"
                     style="display: none;">
                    @foreach($items as $index => $item)
                        @if($item->type !== 'divider')
                            <template x-if="!isVisible({{ $index }})">
                                <div>
                                    @if($item->isSpecialType())
                                        <x-navigation.overflow-special-item :item="$item" />
                                    @elseif($item->hasDropdown())
                                        <x-navigation.overflow-dropdown-item :item="$item" />
                                    @else
                                        <a href="{{ $item->getUrl() }}"
                                           target="{{ $item->open_in_new_tab ? '_blank' : '_self' }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#ff6600] transition-colors">
                                            @if($item->icon)
                                                <x-dynamic-component :component="'heroicon-o-' . $item->icon" class="w-4 h-4 inline mr-1" />
                                            @endif
                                            {{ $item->title }}
                                        </a>
                                    @endif
                                </div>
                            </template>
                        @endif
                    @endforeach
                </div>
            </li>
        </ul>
    </div>
</nav>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('navOverflow', () => ({
        visibleCount: 999,
        itemWidths: [],
        moreButtonWidth: 100,
        initialized: false,

        init() {
            this.$nextTick(() => {
                this.measureItems();
                this.calculateVisibility();
                this.initialized = true;
            });
            window.addEventListener('resize', () => this.calculateVisibility());
        },

        measureItems() {
            const items = this.$refs.navList.querySelectorAll('[data-nav-item]');
            this.itemWidths = Array.from(items).map(el => el.offsetWidth || el.getBoundingClientRect().width);
        },

        calculateVisibility() {
            const container = this.$refs.navList;
            const logo = container.querySelector('[data-logo]');
            const divider = container.querySelector('[data-divider]');

            if (!logo || !container) return;

            const logoWidth = logo.offsetWidth || 0;
            const dividerWidth = divider ? divider.offsetWidth : 0;
            const containerWidth = container.offsetWidth;
            const availableWidth = containerWidth - logoWidth - dividerWidth - this.moreButtonWidth - 20;

            let usedWidth = 0;
            let count = 0;

            for (let width of this.itemWidths) {
                if (usedWidth + width <= availableWidth) {
                    usedWidth += width;
                    count++;
                } else {
                    break;
                }
            }

            this.visibleCount = count;
        },

        isVisible(index) {
            return index < this.visibleCount;
        },

        get hasOverflow() {
            return this.visibleCount < this.itemWidths.length;
        }
    }));
});
</script>
