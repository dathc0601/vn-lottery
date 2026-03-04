@extends('layouts.app')

@section('title', "Dự đoán " . strtoupper($regionSlug) . " - Soi cầu xổ số {$regionName}")

@section('meta_description', "Dự đoán kết quả xổ số {$regionName} (" . strtoupper($regionSlug) . ") hàng ngày. Phân tích thống kê, soi cầu lô đề chính xác nhất.")

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('prediction.index') }}" class="text-[#0066cc] hover:underline">Dự đoán xổ số</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Dự đoán {{ strtoupper($regionSlug) }}</span>
@endsection

@section('page-content')
<div>
    {{-- Tab Navigation --}}
    @include('predictions.partials.hub.tab-navigation', ['regionSlug' => $regionSlug])

    {{-- H1 Title --}}
    <div class="border border-gray-300 bg-white px-4 py-3 mb-4">
        <h1 class="text-xl font-bold text-[#cc0000]">Dự đoán {{ strtoupper($regionSlug) }} - Soi cầu xổ số {{ $regionName }}</h1>
    </div>

    {{-- Intro paragraph --}}
    <div class="bg-white px-4 py-3 mb-4 text-sm text-gray-700 border border-gray-200">
        Chuyên trang <strong>soi cầu {{ strtoupper($regionSlug) }}</strong> cung cấp các dự đoán kết quả xổ số {{ $regionName }}
        dựa trên phân tích thống kê và các thuật toán hiện đại. Dự đoán được cập nhật tự động hàng ngày vào lúc 2h sáng.
    </div>

    {{-- Two-column flex layout --}}
    <div class="flex flex-col lg:flex-row gap-4">

        {{-- Main Content --}}
        <div class="flex-1 min-w-0">

            {{-- Section header --}}
            <div class="bg-gray-200 px-4 py-2 font-semibold text-gray-800 mb-0">
                Danh sách dự đoán {{ strtoupper($regionSlug) }}
            </div>

            {{-- Prediction list --}}
            <div class="bg-white border border-gray-200 border-t-0 px-4">
                @if($predictions->count() > 0)
                    @foreach($predictions as $prediction)
                        <div class="flex gap-4 py-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            {{-- Thumbnail --}}
                            <a href="{{ $prediction->url }}" class="flex-shrink-0">
                                @include('predictions.partials.thumbnail', [
                                    'thumbSlug' => $regionSlug,
                                    'thumbDate' => $prediction->formatted_date,
                                ])
                            </a>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-semibold mb-1">
                                    <a href="{{ $prediction->url }}"
                                       class="text-[#0066cc] hover:text-[#cc0000] transition-colors">
                                        Soi cầu {{ strtoupper($regionSlug) }} {{ $prediction->formatted_date }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600 line-clamp-3">
                                    Dự đoán kết quả {{ strtoupper($regionSlug) }} ngày {{ $prediction->formatted_date }}.
                                    Phân tích thống kê, soi cầu lô đề {{ $regionName }} chính xác nhất.
                                    @php $loto = $prediction->predictions_data['loto_2_digit'] ?? []; @endphp
                                    @if(!empty($loto))
                                        Các cặp loto dự đoán: {{ implode(', ', array_slice($loto, 0, 5)) }}.
                                    @endif
                                </p>
                                <a href="{{ $prediction->url }}"
                                   class="inline-block mt-2 text-sm text-[#0066cc] hover:text-[#cc0000]">
                                    Xem chi tiết &raquo;
                                </a>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="py-8 text-center">
                        <p class="text-gray-500">Chưa có dự đoán nào. Vui lòng quay lại sau.</p>
                    </div>
                @endif
            </div>

            {{-- Pagination --}}
            @if($predictions->hasPages())
                <div class="mt-4">
                    {{ $predictions->links() }}
                </div>
            @endif

            {{-- SEO Content --}}
            <div class="bg-white border border-gray-200 px-4 py-4 mt-4">
                <div class="text-sm text-gray-700 leading-relaxed space-y-3">
                    <h2 class="text-lg font-bold text-gray-900">Dự đoán {{ strtoupper($regionSlug) }} - Soi cầu xổ số {{ $regionName }} chính xác</h2>

                    <p>
                        Trang <strong>dự đoán {{ strtoupper($regionSlug) }}</strong> cung cấp các dự đoán kết quả xổ số {{ $regionName }}
                        dựa trên phân tích thống kê và các thuật toán hiện đại. Dự đoán được cập nhật tự động hàng ngày.
                    </p>

                    <h3 class="text-base font-semibold text-gray-800">Phương pháp dự đoán</h3>
                    <p>
                        Chúng tôi sử dụng phương pháp phân tích thống kê kết hợp với các mô hình dự đoán để đưa ra
                        các con số có xác suất về cao nhất. Các yếu tố được phân tích bao gồm:
                    </p>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        <li>Tần suất xuất hiện của các cặp số trong 30 ngày gần nhất</li>
                        <li>Phân tích lô gan - các số lâu chưa xuất hiện</li>
                        <li>Thống kê đầu đuôi và các quy luật xuất hiện</li>
                        <li>Phân tích giải đặc biệt và các mối liên hệ giữa các giải</li>
                    </ul>

                    <h3 class="text-base font-semibold text-gray-800">Dự đoán các miền khác</h3>
                    <ul class="list-disc list-inside space-y-1 pl-2">
                        @if($regionSlug !== 'xsmb')
                            <li><a href="{{ route('prediction.xsmb.index') }}" class="text-[#0066cc] hover:underline">Dự đoán XSMB</a> - Dự đoán kết quả xổ số Miền Bắc hàng ngày</li>
                        @endif
                        @if($regionSlug !== 'xsmn')
                            <li><a href="{{ route('prediction.xsmn.index') }}" class="text-[#0066cc] hover:underline">Dự đoán XSMN</a> - Dự đoán kết quả xổ số Miền Nam hàng ngày</li>
                        @endif
                        @if($regionSlug !== 'xsmt')
                            <li><a href="{{ route('prediction.xsmt.index') }}" class="text-[#0066cc] hover:underline">Dự đoán XSMT</a> - Dự đoán kết quả xổ số Miền Trung hàng ngày</li>
                        @endif
                    </ul>

                    <p class="italic text-red-600 text-xs mt-4">
                        * Lưu ý: Các dự đoán trên trang này chỉ mang tính chất tham khảo và giải trí.
                        Kết quả xổ số hoàn toàn ngẫu nhiên. Chúng tôi không chịu trách nhiệm về bất kỳ quyết định nào
                        dựa trên các dự đoán này.
                    </p>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        @include('predictions.partials.hub.sidebar')
    </div>
</div>
@endsection
