@props(['item'])

@php
    $url = $item->getUrl();
    $isActive = $item->isActive();
    $target = $item->open_in_new_tab ? '_blank' : '_self';
@endphp

<li>
    <a href="{{ $url }}"
       target="{{ $target }}"
       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
              {{ $isActive ? 'bg-black bg-opacity-15' : '' }}"
       style="line-height: 40px;"
       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
       onmouseout="this.style.background='{{ $isActive ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
        @if($item->icon)
            <x-dynamic-component :component="'heroicon-o-' . $item->icon" class="w-4 h-4 inline mr-1" />
        @endif
        {{ $item->title }}
    </a>
</li>
