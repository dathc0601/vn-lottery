@php
    $activeSlug = $regionSlug ?? null;
    $tabs = [
        ['slug' => 'xsmb', 'label' => 'Dự đoán XSMB', 'route' => 'prediction.xsmb.index'],
        ['slug' => 'xsmn', 'label' => 'Dự đoán XSMN', 'route' => 'prediction.xsmn.index'],
        ['slug' => 'xsmt', 'label' => 'Dự đoán XSMT', 'route' => 'prediction.xsmt.index'],
    ];
@endphp

<div class="bg-white border-b border-gray-300 mb-4">
    <div class="flex items-center gap-0 text-sm">
        @foreach($tabs as $tab)
            <a href="{{ route($tab['route']) }}"
               class="px-4 py-2.5 font-medium border-b-2 transition-colors
                      {{ $activeSlug === $tab['slug']
                          ? 'text-[#cc0000] border-[#cc0000]'
                          : 'text-[#0066cc] hover:text-[#cc0000] border-transparent hover:border-[#cc0000]' }}">
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>
