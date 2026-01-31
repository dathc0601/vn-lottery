@props(['item'])

@inject('navService', 'App\Services\NavigationService')

@if(in_array($item->type, ['xsmb_days', 'xsmt_days', 'xsmn_days']))
    @php
        $type = $item->type;
        $days = $navService->getDaysForType($type);
        $routeBase = $navService->getRouteBaseForRegion($type);
        $mainRoute = $navService->getMainRoute($type);

        $liveLabel = match($type) {
            'xsmb_days' => 'Trực tiếp XSMB',
            'xsmt_days' => 'Trực tiếp XSMT',
            'xsmn_days' => 'Trực tiếp XSMN',
            default => 'Trực tiếp',
        };
    @endphp

    <div class="overflow-special-item" x-data="{ subOpen: false }">
        <a href="{{ route($mainRoute) }}"
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
        {{-- Submenu for days --}}
        <div x-show="subOpen"
             @mouseenter="subOpen = true"
             @mouseleave="subOpen = false"
             class="pl-4 bg-gray-50 border-l-2 border-[#ff6600]">
            @foreach($days as $day)
                <a href="{{ url($routeBase . $day['slug']) }}"
                   class="block px-4 py-2 text-sm text-gray-600 hover:bg-gray-100 hover:text-[#ff6600] transition-colors">
                    {{ $day['name'] }}
                </a>
            @endforeach
            <div class="border-t border-gray-200 mt-1 pt-1">
                <a href="{{ route($mainRoute) }}"
                   class="block px-4 py-2 text-sm text-[#ff6600] font-medium hover:bg-gray-100 transition-colors">
                    {{ $liveLabel }}
                </a>
            </div>
        </div>
    </div>
@endif
