@php
    $seo = app(\App\Services\SiteSettingsService::class);
    $footerService = app(\App\Services\FooterService::class);

    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
    $tagline = $seo->get('general', 'tagline', 'Số chuẩn xác - May mắn phát');

    $introTitle = $seo->get('footer', 'intro_title', $siteName);
    $introText = $seo->get('footer', 'intro_text', '');
    $infoTableRows = $footerService->getInfoTableRows();
    $notes = $footerService->getNotes();
    $referenceLinks = $footerService->getReferenceLinks();
    $showSchedule = $seo->get('footer', 'show_schedule', '1');
    $showBottomNav = $seo->get('footer', 'show_bottom_nav', '1');
    $copyrightText = $seo->copyrightText();
    $disclaimerText = $seo->get('footer', 'disclaimer_text', 'Kết quả chỉ mang tính chất tham khảo');
@endphp

<footer class="mt-12">
    {{-- Section 1: Introduction --}}
    @if($introText || count($infoTableRows) > 0)
    <div class="bg-gray-50 border-t border-gray-200">
        <div class="container mx-auto px-4 py-6" style="max-width: 1040px;">
            @if($introTitle)
                <h2 class="text-lg font-bold text-gray-800 mb-1">
                    {{ $introTitle }}
                </h2>
                <div class="w-16 h-0.5 bg-[#ff6600] mb-4"></div>
            @endif

            @if($introText)
                <div class="text-sm text-gray-600 leading-relaxed mb-4">{!! nl2br(e($introText)) !!}</div>
            @endif

            @if(count($infoTableRows) > 0)
                <table class="w-full text-sm border-collapse">
                    @foreach($infoTableRows as $row)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 pr-4 font-semibold text-gray-700 whitespace-nowrap w-1/3">{{ $row['label'] ?? '' }}</td>
                            <td class="py-2 text-gray-600">{{ $row['value'] ?? '' }}</td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
    @endif

    {{-- Section 2: Schedule Table --}}
    @if($showSchedule)
    <div class="bg-white border-t border-gray-200">
        <div class="container mx-auto px-4 py-6" style="max-width: 1040px;">
            <h3 class="text-base font-bold text-gray-800 mb-3">Lịch mở thưởng xổ số các tỉnh</h3>
            <x-footer.schedule-table />
        </div>
    </div>
    @endif

    {{-- Section 3: Notes --}}
    @if(count($notes) > 0)
    <div class="bg-white border-t border-gray-200">
        <div class="container mx-auto px-4 py-6" style="max-width: 1040px;">
            <h3 class="text-base font-bold text-gray-800 mb-3">Lưu ý</h3>
            <ul class="space-y-2">
                @foreach($notes as $note)
                    <li class="flex items-start text-sm text-gray-600">
                        <span class="inline-block w-2 h-2 rounded-full bg-[#ff6600] mt-1.5 mr-3 flex-shrink-0"></span>
                        <span>{{ $note['text'] ?? $note }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Section 4: Reference Links --}}
    @if(count($referenceLinks) > 0)
    <div class="bg-gray-50 border-t border-gray-200">
        <div class="container mx-auto px-4 py-4" style="max-width: 1040px;">
            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                <span class="font-semibold text-gray-700">Tham khảo:</span>
                @foreach($referenceLinks as $link)
                    <a href="{{ $link['url'] ?? '#' }}"
                       class="text-[#0066cc] hover:text-[#ff6600] hover:underline"
                       @if($link['new_tab'] ?? false) target="_blank" rel="noopener noreferrer" @endif
                    >{{ $link['label'] ?? '' }}</a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Section 5: Bottom Navigation Bar --}}
    @if($showBottomNav)
        <x-footer.bottom-nav />
    @endif

    {{-- Section 6: Copyright / Disclaimer --}}
    <div class="bg-[#2d2d2d] text-center py-4">
        <div class="container mx-auto px-4" style="max-width: 1040px;">
            <p class="text-xs text-gray-400">{{ $copyrightText }}</p>
            @if($disclaimerText)
                <p class="text-xs text-gray-500 mt-1">{{ $disclaimerText }}</p>
            @endif
        </div>
    </div>

    {{-- Section 7: SEO Footer Scripts --}}
    <x-seo-footer-scripts />
</footer>
