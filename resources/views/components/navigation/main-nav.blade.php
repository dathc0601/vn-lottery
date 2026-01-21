@inject('navService', 'App\Services\NavigationService')

@php
    $items = $navService->getNavigation();
@endphp

<nav class="bg-[#ff6600]">
    <div class="container mx-auto px-4" style="max-width: 1400px;">
        <ul class="flex items-center h-10 flex-wrap">
            @foreach($items as $item)
                @if($item->type === 'divider')
                    <li class="border-l border-white/30 h-5 mx-1"></li>
                @elseif($item->isSpecialType())
                    <x-navigation.special-item :item="$item" />
                @elseif($item->hasDropdown())
                    <x-navigation.nav-dropdown :item="$item" />
                @else
                    <x-navigation.nav-item :item="$item" />
                @endif
            @endforeach
        </ul>
    </div>
</nav>
