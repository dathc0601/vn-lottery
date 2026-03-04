@extends('layouts.app')

@section('title', 'Dự đoán xổ số - Soi cầu XSMB, XSMT, XSMN')

@section('meta_description', 'Dự đoán kết quả xổ số Miền Bắc, Miền Trung, Miền Nam hàng ngày. Phân tích thống kê, soi cầu lô đề chính xác nhất.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Dự đoán xổ số</span>
@endsection

@section('page-content')
<div>
    {{-- Tab Navigation --}}
    @include('predictions.partials.hub.tab-navigation')

    {{-- H1 Title --}}
    <div class="border border-gray-300 bg-white px-4 py-3 mb-4">
        <h1 class="text-xl font-bold text-[#cc0000]">Dự đoán xổ số - Soi cầu XSMB, XSMT, XSMN</h1>
    </div>

    {{-- Intro paragraph --}}
    <div class="bg-white px-4 py-3 mb-4 text-sm text-gray-700 border border-gray-200">
        Chuyên trang <strong>dự đoán xổ số</strong> cung cấp các dự đoán kết quả xổ số 3 miền Bắc, Trung, Nam
        dựa trên phân tích thống kê và các thuật toán hiện đại. Dự đoán được cập nhật tự động hàng ngày vào lúc 2h sáng.
    </div>

    {{-- Two-column flex layout --}}
    <div class="flex flex-col lg:flex-row gap-4">

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">

            {{-- Section header --}}
            <div class="bg-gray-200 px-4 py-2 font-semibold text-gray-800 mb-0">
                Dự đoán xổ số hôm nay
            </div>

            {{-- Prediction cards --}}
            <div class="bg-white border border-gray-200 border-t-0 px-4">
                @include('predictions.partials.hub.prediction-card-list')
            </div>

            {{-- "Xem thêm" link --}}
            <div class="text-center py-3">
                <a href="{{ route('prediction.xsmb.index') }}" class="text-[#0066cc] hover:text-[#cc0000] text-sm font-medium">
                    Xem thêm dự đoán &raquo;
                </a>
            </div>

            {{-- SEO Content --}}
            <div class="bg-white border border-gray-200 px-4 py-4">
                @include('predictions.partials.hub.seo-content')
            </div>

        </div>

        {{-- Sidebar --}}
        @include('predictions.partials.hub.sidebar')
    </div>
</div>
@endsection
