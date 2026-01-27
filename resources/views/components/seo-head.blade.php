@php
    $seo = app(\App\Services\SiteSettingsService::class);
    $override = app(\App\Services\SeoOverrideService::class);
    $siteName = $seo->get('general', 'site_name', 'XSKT.VN');
@endphp

{{-- Favicon --}}
@if($faviconUrl = $seo->imageUrl('general', 'favicon'))
    <link rel="icon" href="{{ $faviconUrl }}">
@endif

{{-- Apple Touch Icon --}}
@if($appleTouchUrl = $seo->imageUrl('general', 'apple_touch_icon'))
    <link rel="apple-touch-icon" href="{{ $appleTouchUrl }}">
@endif

{{-- Meta Description --}}
@if($override->has('meta_description'))
    <meta name="description" content="{{ $override->get('meta_description') }}">
@elseif(View::hasSection('meta_description'))
    <meta name="description" content="@yield('meta_description')">
@elseif($defaultDesc = $seo->get('meta', 'default_description'))
    <meta name="description" content="{{ $defaultDesc }}">
@endif

{{-- Meta Keywords --}}
@if($override->has('meta_keywords'))
    <meta name="keywords" content="{{ $override->get('meta_keywords') }}">
@elseif($keywords = $seo->get('meta', 'default_keywords'))
    <meta name="keywords" content="{{ $keywords }}">
@endif

{{-- Meta Author --}}
@if($author = $seo->get('meta', 'meta_author'))
    <meta name="author" content="{{ $author }}">
@endif

{{-- Robots --}}
@if($override->has('robots'))
    <meta name="robots" content="{{ $override->get('robots') }}">
@else
    <meta name="robots" content="{{ $seo->robotsMeta() }}">
@endif

{{-- Canonical --}}
@if($override->has('canonical_url'))
    <link rel="canonical" href="{{ $override->get('canonical_url') }}">
@elseif(View::hasSection('canonical'))
    <link rel="canonical" href="@yield('canonical')">
@elseif($canonicalPrefix = $seo->get('advanced', 'canonical_prefix'))
    <link rel="canonical" href="{{ $canonicalPrefix }}{{ request()->getPathInfo() }}">
@endif

{{-- Open Graph --}}
<meta property="og:type" content="{{ $seo->get('og', 'type', 'website') }}">
<meta property="og:locale" content="{{ $seo->get('og', 'locale', 'vi_VN') }}">
<meta property="og:site_name" content="{{ $seo->get('og', 'site_name') ?: $siteName }}">

@if($override->has('og_title'))
    <meta property="og:title" content="{{ $override->get('og_title') }}">
@elseif(View::hasSection('og_title'))
    <meta property="og:title" content="@yield('og_title')">
@else
    <meta property="og:title" content="{{ $siteName }}">
@endif

@if($override->has('og_description'))
    <meta property="og:description" content="{{ $override->get('og_description') }}">
@elseif(View::hasSection('meta_description'))
    <meta property="og:description" content="@yield('meta_description')">
@elseif($defaultDesc = $seo->get('meta', 'default_description'))
    <meta property="og:description" content="{{ $defaultDesc }}">
@endif

<meta property="og:url" content="{{ url()->current() }}">

@if($override->has('og_image'))
    <meta property="og:image" content="{{ $override->get('og_image') }}">
@elseif(View::hasSection('og_image'))
    <meta property="og:image" content="@yield('og_image')">
@elseif($ogImage = $seo->imageUrl('og', 'default_image'))
    <meta property="og:image" content="{{ $ogImage }}">
@endif

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $seo->get('twitter', 'card_type', 'summary_large_image') }}">

@if($twitterHandle = $seo->get('twitter', 'site_handle'))
    <meta name="twitter:site" content="@{{ $twitterHandle }}">
@endif

@if($override->has('og_title'))
    <meta name="twitter:title" content="{{ $override->get('og_title') }}">
@elseif(View::hasSection('og_title'))
    <meta name="twitter:title" content="@yield('og_title')">
@else
    <meta name="twitter:title" content="{{ $siteName }}">
@endif

@if($override->has('og_description'))
    <meta name="twitter:description" content="{{ $override->get('og_description') }}">
@elseif(View::hasSection('meta_description'))
    <meta name="twitter:description" content="@yield('meta_description')">
@elseif($defaultDesc = $seo->get('meta', 'default_description'))
    <meta name="twitter:description" content="{{ $defaultDesc }}">
@endif

@if($override->has('og_image'))
    <meta name="twitter:image" content="{{ $override->get('og_image') }}">
@elseif(View::hasSection('og_image'))
    <meta name="twitter:image" content="@yield('og_image')">
@elseif($twitterImage = $seo->imageUrl('twitter', 'default_image'))
    <meta name="twitter:image" content="{{ $twitterImage }}">
@elseif($ogImage = $seo->imageUrl('og', 'default_image'))
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif

{{-- Google Search Console Verification --}}
@if($gsc = $seo->get('analytics', 'google_search_console'))
    <meta name="google-site-verification" content="{{ $gsc }}">
@endif

{{-- Bing Webmaster Verification --}}
@if($bing = $seo->get('analytics', 'bing_webmaster'))
    <meta name="msvalidate.01" content="{{ $bing }}">
@endif

{{-- Google Analytics 4 --}}
@if($ga4 = $seo->get('analytics', 'ga4_id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $ga4 }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $ga4 }}');
    </script>
@endif

{{-- Google Tag Manager --}}
@if($gtm = $seo->get('analytics', 'gtm_id'))
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $gtm }}');
    </script>
@endif

{{-- Facebook Pixel --}}
@if($fbPixel = $seo->get('analytics', 'facebook_pixel_id'))
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ $fbPixel }}');
        fbq('track', 'PageView');
    </script>
@endif

{{-- Organization Schema.org JSON-LD --}}
@if($orgSchema = $seo->organizationSchema())
    <script type="application/ld+json">{!! $orgSchema !!}</script>
@endif

{{-- SEO Override: Custom JSON-LD --}}
@if($override->has('schema_jsonld'))
    <script type="application/ld+json">{!! $override->get('schema_jsonld') !!}</script>
@endif

{{-- Custom Head Scripts --}}
@if($headScripts = $seo->get('analytics', 'custom_head_scripts'))
    {!! $headScripts !!}
@endif
