@props(['item'])

@php
    $url = $item->getUrl();
    $target = $item->open_in_new_tab ? '_blank' : '_self';
@endphp

<div class="overflow-dropdown-item" x-data="{ subOpen: false }">
    <a href="{{ $url }}"
       target="{{ $target }}"
       class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-[#ff6600] transition-colors"
       @mouseenter="subOpen = true"
       @mouseleave="subOpen = false">
        @if($item->icon)
            <x-dynamic-component :component="'heroicon-o-' . $item->icon" class="w-4 h-4 inline mr-1" />
        @endif
        <span class="flex-1">{{ $item->title }}</span>
        <svg class="w-3 h-3 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </a>
    {{-- Submenu for children --}}
    @if($item->children && count($item->children) > 0)
        <div x-show="subOpen"
             @mouseenter="subOpen = true"
             @mouseleave="subOpen = false"
             class="pl-4 bg-gray-50 border-l-2 border-[#ff6600]">
            @foreach($item->children as $child)
                @if($child->type === 'divider')
                    <div class="border-t border-gray-200 my-1"></div>
                @else
                    <a href="{{ $child->getUrl() }}"
                       target="{{ $child->open_in_new_tab ? '_blank' : '_self' }}"
                       class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-[#ff6600] transition-colors">
                        {{ $child->title }}
                    </a>
                @endif
            @endforeach
        </div>
    @endif
</div>
