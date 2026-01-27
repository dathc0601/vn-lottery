@php
    $seo = app(\App\Services\SiteSettingsService::class);
@endphp

{{-- Google Tag Manager (noscript) --}}
@if($gtm = $seo->get('analytics', 'gtm_id'))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtm }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
@endif

{{-- Facebook Pixel (noscript) --}}
@if($fbPixel = $seo->get('analytics', 'facebook_pixel_id'))
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $fbPixel }}&ev=PageView&noscript=1" alt=""></noscript>
@endif

{{-- Custom Body Scripts --}}
@if($bodyScripts = $seo->get('analytics', 'custom_body_scripts'))
    {!! $bodyScripts !!}
@endif
