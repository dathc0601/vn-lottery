@php
    $seo = app(\App\Services\SiteSettingsService::class);
    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
    $tagline = $seo->get('general', 'tagline', 'Số chuẩn xác - May mắn phát');
    $headerSubtitle = $seo->get('general', 'header_subtitle', $tagline);
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
    <!-- Navigation Bar with Logo -->
    <x-navigation.main-nav />

    <!-- Breadcrumb (optional) -->
    @hasSection('breadcrumb')
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-4 py-2 text-sm" style="max-width: 1040px;">
            @yield('breadcrumb')
        </div>
    </div>
    @endif

    <!-- Main Content (flexible) -->
    @yield('content')

    <!-- Footer -->
    <x-footer.site-footer />

    @yield('scripts')
    @stack('scripts')
</body>
</html>
