@php
    $seo = app(\App\Services\SiteSettingsService::class);
    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
    $tagline = $seo->get('general', 'tagline', 'Số chuẩn xác - May mắn phát');
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php $overrideSvc = app(\App\Services\SeoOverrideService::class); @endphp
    <title>{{ $overrideSvc->has('page_title') ? $overrideSvc->get('page_title') : View::yieldContent('title', 'Kết Quả Xổ Số - ' . $siteName) }}</title>

    <!-- Google Fonts - Roboto + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <x-seo-head />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased">
    <x-seo-body-open />
    <!-- Dark Header - xskt.net style -->
    <header class="bg-[#2d2d2d] text-white py-3">
        <div class="container mx-auto px-4" style="max-width: 1400px;">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('home') }}" class="block">
                        @if($logoUrl = $seo->imageUrl('general', 'site_logo'))
                            <img src="{{ $logoUrl }}" alt="{{ $siteName }}" class="h-8 md:h-10">
                        @else
                            <h1 class="text-xl md:text-2xl font-bold text-[#ff6600]">{{ $siteName }}</h1>
                        @endif
                        <p class="text-xs text-gray-300 mt-0.5">{{ $tagline }}</p>
                    </a>
                </div>
                <div class="text-right">
                    <p class="text-sm">Hôm nay: {{ now()->locale('vi')->isoFormat('dddd [ngày] DD/MM/YYYY') }}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Orange Navigation Bar - Database driven -->
    <x-navigation.main-nav />

    <!-- Breadcrumb (optional) -->
    @hasSection('breadcrumb')
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-4 py-2 text-sm" style="max-width: 1400px;">
            @yield('breadcrumb')
        </div>
    </div>
    @endif

    <!-- Main Content (flexible) -->
    @yield('content')

    <!-- Footer - Dark Theme (Database-driven) -->
    @php
        $footerColumns = app(\App\Services\FooterService::class)->getColumns();
    @endphp
    <footer class="bg-[#333333] text-white mt-12">
        <div class="container mx-auto px-4 py-8" style="max-width: 1400px;">
            <div class="grid md:grid-cols-{{ $footerColumns->count() ?: 3 }} gap-8">
                @foreach($footerColumns as $footerCol)
                    <div>
                        @if($footerCol->type === 'about')
                            <h3 class="font-bold text-lg mb-3">{{ $siteName }}</h3>
                            <p class="text-sm text-gray-300">{{ $seo->get('footer', 'about_text', 'Trang web cung cấp kết quả xổ số 3 miền nhanh nhất và chính xác nhất') }}</p>
                        @elseif($footerCol->type === 'links')
                            <h3 class="font-bold text-lg mb-3">{{ $footerCol->title }}</h3>
                            <ul class="space-y-2 text-sm">
                                @foreach($footerCol->activeLinks as $footerLink)
                                    @php $linkUrl = $footerLink->getUrl(); @endphp
                                    @if($linkUrl)
                                        <li>
                                            <a href="{{ $linkUrl }}"
                                               class="text-gray-300 hover:text-[#ff6600] hover:underline transition-colors"
                                               @if($footerLink->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                            >{{ $footerLink->label }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @elseif($footerCol->type === 'info')
                            <h3 class="font-bold text-lg mb-3">{{ $footerCol->title }}</h3>
                            <p class="text-sm text-gray-300">{{ $seo->copyrightText() }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $seo->get('footer', 'disclaimer_text', 'Kết quả chỉ mang tính chất tham khảo') }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $tagline }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <x-seo-footer-scripts />
    </footer>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
