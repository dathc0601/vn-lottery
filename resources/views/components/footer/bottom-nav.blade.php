@inject('navService', 'App\Services\NavigationService')

@php
    $items = $navService->getNavigation();
@endphp

<div class="bg-[#333333] border-t border-gray-600">
    <div class="container mx-auto px-4 py-3" style="max-width: 1040px;">
        <div class="flex flex-wrap items-center justify-center gap-y-1">
            @foreach($items as $item)
                @if($item->type === 'divider')
                    @continue
                @endif

                @if(!$loop->first)
                    <span class="text-gray-500 mx-2">|</span>
                @endif

                @php $itemUrl = $item->getUrl(); @endphp
                @if($itemUrl)
                    <a href="{{ $itemUrl }}"
                       class="text-gray-300 hover:text-[#ff6600] text-sm transition-colors"
                       @if($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                    >{{ $item->title }}</a>
                @else
                    <span class="text-gray-300 text-sm">{{ $item->title }}</span>
                @endif
            @endforeach
        </div>
    </div>
</div>
