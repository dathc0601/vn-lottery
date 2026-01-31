@extends('layouts.app')

@section('title', "Dự đoán " . strtoupper($regionSlug) . " - Soi cầu xổ số {$regionName}")

@section('meta_description', "Dự đoán kết quả xổ số {$regionName} (" . strtoupper($regionSlug) . ") hàng ngày. Phân tích thống kê, soi cầu lô đề chính xác nhất.")

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Dự đoán {{ strtoupper($regionSlug) }}</span>
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
                    Dự đoán {{ strtoupper($regionSlug) }} - Soi cầu xổ số {{ $regionName }}
                </div>
            </div>

            <!-- Introduction -->
            <div class="bg-white rounded shadow p-4 mb-4">
                <p class="text-gray-700">
                    Chuyên trang <strong>soi cầu {{ strtoupper($regionSlug) }}</strong> cung cấp các dự đoán kết quả xổ số {{ $regionName }}
                    dựa trên phân tích thống kê và các thuật toán hiện đại. Dự đoán được cập nhật tự động hàng ngày vào lúc 2h sáng.
                </p>
            </div>

            {{-- Predictions List --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Danh sách dự đoán {{ strtoupper($regionSlug) }}
                </h2>

                @if($predictions->count() > 0)
                    <div class="block">
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
        @include('predictions.partials.sidebar', [
            'region' => $region,
            'regionSlug' => $regionSlug,
            'relatedPredictions' => $relatedPredictions
        ])
    </div>
</div>
@endsection
