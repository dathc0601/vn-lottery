@php
    $seo = app(\App\Services\SiteSettingsService::class);
@endphp

{{-- Custom Footer Scripts --}}
@if($footerScripts = $seo->get('analytics', 'custom_footer_scripts'))
    {!! $footerScripts !!}
@endif
