@inject('navService', 'App\Services\NavigationService')

@php
    $items = $navService->getNavigation();
    $seo = app(\App\Services\SiteSettingsService::class);
    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
    $logoUrl = $seo->imageUrl('general', 'site_logo');
@endphp

<nav class="bg-[#ff6600]">
    <div class="container mx-auto px-4" style="max-width: 1040px;">
        <ul class="flex items-center h-10 flex-wrap">
            <li class="mr-2">
                <a href="{{ route('home') }}" class="flex items-center h-10">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-7">
                    @else
                        <span class="font-bold text-white text-lg">{{ $siteName }}</span>
                    @endif
                </a>
            </li>
            <li class="border-l border-white/30 h-5 mx-1"></li>
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
