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
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <!-- Page Header (Orange bar) -->
            <div class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    Dự đoán xổ số - Soi cầu XSMB, XSMT, XSMN
                </div>
            </div>

            <!-- Introduction -->
            <div class="bg-white rounded shadow p-4 mb-4">
                <p class="text-gray-700">
                    Chuyên trang <strong>dự đoán xổ số</strong> cung cấp các dự đoán kết quả xổ số 3 miền Bắc, Trung, Nam
                    dựa trên phân tích thống kê và các thuật toán hiện đại. Dự đoán được cập nhật tự động hàng ngày vào lúc 2h sáng.
                </p>
            </div>

            <!-- Latest Predictions by Region -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Dự đoán mới nhất hôm nay
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach(\App\Models\Prediction::REGIONS as $region => $regionName)
                        @php
                            $latest = $latestByRegion[$region] ?? null;
                            $regionSlug = \App\Models\Prediction::REGION_SLUGS[$region];
                            $colors = [
                                'north' => ['bg' => 'bg-red-50', 'border' => 'border-red-400', 'text' => 'text-red-700', 'badge' => 'bg-red-500'],
                                'central' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-400', 'text' => 'text-yellow-700', 'badge' => 'bg-yellow-500'],
                                'south' => ['bg' => 'bg-green-50', 'border' => 'border-green-400', 'text' => 'text-green-700', 'badge' => 'bg-green-500'],
                            ];
                            $color = $colors[$region];
                        @endphp
                        <div class="rounded-lg border-2 {{ $color['bg'] }} {{ $color['border'] }} overflow-hidden">
                            <div class="{{ $color['badge'] }} text-white px-4 py-2 font-semibold flex items-center justify-between">
                                <span>{{ strtoupper($regionSlug) }}</span>
                                <span class="text-sm opacity-90">{{ $regionName }}</span>
                            </div>
                            <div class="p-4">
                                @if($latest)
                                    <div class="mb-3">
                                        <span class="text-sm text-gray-500">Ngày dự đoán:</span>
                                        <span class="font-semibold {{ $color['text'] }}">{{ $latest->formatted_date }}</span>
                                    </div>
                                    @php
                                        $loto2Digit = $latest->predictions_data['loto_2_digit'] ?? [];
                                    @endphp
                                    @if(!empty($loto2Digit))
                                        <div class="mb-3">
                                            <span class="text-xs text-gray-500 block mb-1">Loto 2 số hay về:</span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($loto2Digit, 0, 5) as $num)
                                                    <span class="inline-block px-2 py-0.5 bg-white rounded text-sm font-bold {{ $color['text'] }} shadow-sm">
                                                        {{ $num }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <a href="{{ $latest->url }}"
                                       class="inline-flex items-center text-sm font-medium {{ $color['text'] }} hover:underline">
                                        Xem chi tiết
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                @else
                                    <p class="text-gray-500 text-sm">Chưa có dự đoán</p>
                                @endif
                            </div>
                            <div class="px-4 pb-3">
                                <a href="{{ route('prediction.' . $regionSlug . '.index') }}"
                                   class="text-xs text-gray-500 hover:text-gray-700">
                                    Xem tất cả dự đoán {{ strtoupper($regionSlug) }} &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- All Predictions List --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Tất cả dự đoán
                </h2>

                @if($predictions->count() > 0)
                    <div class="space-y-3">
                        @foreach($predictions as $prediction)
                            @include('predictions.components.prediction-card', ['prediction' => $prediction])
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($predictions->hasPages())
                        <div class="mt-6">
                            {{ $predictions->links() }}
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-600">Chưa có dự đoán nào. Vui lòng quay lại sau.</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Sidebar -->
        @include('predictions.partials.sidebar-all')
    </div>
</div>
@endsection
