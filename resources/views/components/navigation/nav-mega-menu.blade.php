@props(['item', 'region'])

@inject('navService', 'App\Services\NavigationService')

@php
    $provincesByDay = $navService->getProvincesByDay($region);
    $dayNames = $navService->getDayNames();
    $routeBase = $region === 'central' ? '/xsmt/' : '/xsmn/';
    $mainRoute = $region === 'central' ? 'xsmt' : 'xsmn';
    $viewAllLabel = $region === 'central' ? 'Xem tất cả XSMT' : 'Xem tất cả XSMN';
    $isActive = request()->routeIs($mainRoute . '*');
@endphp

<li class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
    <a href="{{ route($mainRoute) }}"
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
         class="absolute left-0 top-full bg-white shadow-lg rounded-b-md z-50 py-3 px-4"
         style="display: none; min-width: 500px;">
        <div class="grid grid-cols-7 gap-2">
            @foreach($dayNames as $dayNum => $dayName)
                <div class="min-w-[65px]">
                    <div class="text-xs font-semibold text-gray-500 mb-2 pb-1 border-b border-gray-200">{{ $dayName }}</div>
                    @if(isset($provincesByDay[$dayNum]))
                        @foreach($provincesByDay[$dayNum] as $province)
                            <a href="{{ url($routeBase . $province->slug) }}"
                               class="block text-xs py-1 text-gray-700 hover:text-[#ff6600] transition-colors whitespace-nowrap">
                                {{ $province->name }}
                            </a>
                        @endforeach
                    @else
                        <span class="text-xs text-gray-400">-</span>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="border-t border-gray-200 mt-3 pt-2">
            <a href="{{ route($mainRoute) }}"
               class="text-sm text-[#ff6600] font-medium hover:underline">
                {{ $viewAllLabel }} &rarr;
            </a>
        </div>
    </div>
</li>
