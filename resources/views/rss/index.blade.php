@extends('layouts.app')

@section('title', 'RSS Feed - Kết Quả Xổ Số')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-2">RSS Feed Kết Quả Xổ Số</h1>
    <p class="text-gray-600 mb-4">Nhấn vào tên tỉnh/miền để mở RSS feed. Sao chép link và thêm vào trình đọc RSS của bạn.</p>

    {{-- Regional Feeds --}}
    <div class="border-t border-gray-300 pt-3 mb-4">
        <h2 class="text-lg font-semibold text-gray-800 mb-2">Theo Miền</h2>
        <ul class="space-y-1 text-base">
            <li><a href="{{ route('rssfeed.xsmb') }}" class="text-blue-700 underline hover:text-blue-900">Xổ Số Miền Bắc (XSMB)</a></li>
            <li><a href="{{ route('rssfeed.xsmt') }}" class="text-blue-700 underline hover:text-blue-900">Xổ Số Miền Trung (XSMT)</a></li>
            <li><a href="{{ route('rssfeed.xsmn') }}" class="text-blue-700 underline hover:text-blue-900">Xổ Số Miền Nam (XSMN)</a></li>
        </ul>
    </div>

    {{-- North Provinces --}}
    @if(!empty($provincesGrouped['north']))
    <div class="border-t border-gray-300 pt-3 mb-4">
        <h2 class="text-lg font-semibold text-blue-800 mb-2">Miền Bắc</h2>
        <div class="text-base leading-relaxed">
            @foreach($provincesGrouped['north'] as $item)
                <a href="{{ route('rssfeed.province', ['code' => $item['rss_code']]) }}" class="text-blue-700 underline hover:text-blue-900 whitespace-nowrap">{{ $item['province']->name }}</a>@if(!$loop->last)<span class="text-gray-400 mx-1">|</span>@endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Central Provinces --}}
    @if(!empty($provincesGrouped['central']))
    <div class="border-t border-gray-300 pt-3 mb-4">
        <h2 class="text-lg font-semibold text-purple-800 mb-2">Miền Trung</h2>
        <div class="text-base leading-relaxed">
            @foreach($provincesGrouped['central'] as $item)
                <a href="{{ route('rssfeed.province', ['code' => $item['rss_code']]) }}" class="text-purple-700 underline hover:text-purple-900 whitespace-nowrap">{{ $item['province']->name }}</a>@if(!$loop->last)<span class="text-gray-400 mx-1">|</span>@endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- South Provinces --}}
    @if(!empty($provincesGrouped['south']))
    <div class="border-t border-gray-300 pt-3 mb-4">
        <h2 class="text-lg font-semibold text-green-800 mb-2">Miền Nam</h2>
        <div class="text-base leading-relaxed">
            @foreach($provincesGrouped['south'] as $item)
                <a href="{{ route('rssfeed.province', ['code' => $item['rss_code']]) }}" class="text-green-700 underline hover:text-green-900 whitespace-nowrap">{{ $item['province']->name }}</a>@if(!$loop->last)<span class="text-gray-400 mx-1">|</span>@endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Usage Instructions --}}
    <div class="border-t border-gray-300 pt-3">
        <h3 class="font-semibold text-gray-800 mb-1">Hướng dẫn sử dụng</h3>
        <ol class="list-decimal list-inside text-gray-700 text-base">
            <li>Nhấn vào tên tỉnh/miền để mở RSS feed</li>
            <li>Sao chép link từ thanh địa chỉ trình duyệt</li>
            <li>Mở ứng dụng đọc RSS (Feedly, Inoreader, RSS Reader...)</li>
            <li>Dán link RSS vào ứng dụng để theo dõi</li>
        </ol>
    </div>
</div>
@endsection
