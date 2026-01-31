@props(['item', 'index' => null])

@php
    $url = $item->getUrl();
    $isActive = $item->isActive();
    $target = $item->open_in_new_tab ? '_blank' : '_self';
@endphp

<li class="relative shrink-0"
    x-data="{ open: false }"
    @mouseenter="open = true"
    @mouseleave="open = false"
    @if($index !== null)
        data-nav-item="{{ $index }}"
        x-show="isVisible({{ $index }})"
        x-cloak
    @endif
>
    <a href="{{ $url }}"
       target="{{ $target }}"
       class="flex items-center px-4 text-white font-medium transition-colors duration-200 text-sm
              {{ $isActive ? 'bg-black bg-opacity-15' : '' }}"
       style="line-height: 40px;"
       :style="open ? 'background: rgba(0,0,0,0.15)' : '{{ $isActive ? 'background: rgba(0,0,0,0.15)' : '' }}'">
        @if($item->icon)
            <x-dynamic-component :component="'heroicon-o-' . $item->icon" class="w-4 h-4 inline mr-1" />
        @endif
        {{ $item->title }}
        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </a>
    <div x-show="open"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 transform -translate-y-1"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-1"
         class="absolute left-0 top-full bg-white shadow-lg rounded-b-md z-50 min-w-[160px] py-1"
         style="display: none;">
        @foreach($item->children as $child)
            @if($child->type === 'divider')
                <div class="border-t border-gray-200 my-1"></div>
            @else
                <a href="{{ $child->getUrl() }}"
                   target="{{ $child->open_in_new_tab ? '_blank' : '_self' }}"
                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#ff6600] transition-colors">
                    {{ $child->title }}
                </a>
            @endif
        @endforeach
    </div>
</li>
