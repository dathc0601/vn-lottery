@extends('layouts.app')

@section('title', "Soi cầu KQ" . strtoupper($regionSlug) . " " . $prediction->formatted_date . " - Dự đoán xổ số " . $regionName)

@section('meta_description', "Dự đoán kết quả xổ số {$regionName} ngày {$prediction->formatted_date}. Phân tích thống kê, soi cầu lô đề chính xác nhất hôm nay.")

@section('og_image', route('og-image.prediction', ['regionSlug' => $regionSlug, 'date' => $prediction->prediction_date->format('d-m-Y')]))

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('prediction.index') }}" class="text-[#0066cc] hover:underline">Dự đoán xổ số</a>
    <span class="mx-1">/</span>
    <a href="{{ route('prediction.' . $regionSlug . '.index') }}" class="text-[#0066cc] hover:underline">Dự đoán {{ strtoupper($regionSlug) }}</a>
@endsection

@push('head')
    {{-- JSON-LD Article Schema --}}
    @php
        $jsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => 'Soi cầu KQ' . strtoupper($regionSlug) . ' ' . $prediction->formatted_date . ' - Dự đoán xổ số ' . $regionName,
            'datePublished' => $prediction->published_at?->toIso8601String(),
            'dateModified' => $prediction->updated_at->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
        ];
        if ($author) {
            $jsonLd['author'] = [
                '@type' => 'Person',
                'name' => $author->name,
            ];
        }
    @endphp
    <script type="application/ld+json">
        @json($jsonLd, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
    </script>
@endpush

@section('page-content')
<div>
    {{-- Tab Navigation --}}
    @include('predictions.partials.hub.tab-navigation', ['regionSlug' => $regionSlug])

    @if($regionSlug === 'xsmb')
        {{-- ========== XSMB BRANCH (Redesigned) ========== --}}
        @php
            $predDate = $prediction->prediction_date;
            $refDate = $prediction->reference_date;
            $formattedDate = $predDate->format('d/m/Y');
            $formattedDateDash = $predDate->format('d-m-Y');
            $weekdays = [0 => 'Chủ Nhật', 1 => 'Thứ Hai', 2 => 'Thứ Ba', 3 => 'Thứ Tư', 4 => 'Thứ Năm', 5 => 'Thứ Sáu', 6 => 'Thứ Bảy'];
            $weekdayName = $weekdays[$predDate->dayOfWeek] ?? '';
            $refDateFormatted = $refDate instanceof \Carbon\Carbon ? $refDate->format('d/m/Y') : date('d/m/Y', strtotime($refDate));

            // Get previous day special prize from lottery results snapshot
            $refResult = is_array($prediction->lottery_results_snapshot) ? ($prediction->lottery_results_snapshot[0] ?? null) : null;
            $prevSpecialPrize = $refResult['prize_special'] ?? null;
        @endphp

        {{-- H1 in dashed-border box --}}
        <div class="border-2 border-dashed border-gray-400 bg-white px-4 py-3 mb-4">
            <h1 class="text-xl font-bold text-[#cc0000]">
                Soi cầu KQXSMB {{ $formattedDate }} ({{ $weekdayName }}), Dự đoán xổ số Miền Bắc {{ $formattedDateDash }}
            </h1>
        </div>

        {{-- Two-column flex layout --}}
        <div class="flex flex-col lg:flex-row gap-4">

            {{-- Main Content --}}
            <div class="flex-1 min-w-0">
                <div class="bg-white border border-gray-200 overflow-hidden mb-4">
                    <div class="p-4 md:p-6">

                        {{-- Short intro paragraph --}}
                        <p class="text-sm text-gray-700 mb-4">
                            Chuyên trang <strong>soi cầu XSMB</strong> ngày {{ $formattedDate }} cung cấp dự đoán kết quả xổ số Miền Bắc
                            dựa trên phân tích thống kê và các thuật toán hiện đại. Cập nhật mỗi ngày vào lúc 2h sáng.
                        </p>

                        {{-- Author info (INLINE) --}}
                        <div class="flex items-center gap-3 text-sm text-gray-600 mb-6 pb-4 border-b border-gray-200">
                            @if($author)
                                <div class="w-8 h-8 rounded-full bg-[#8B2500] text-white flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(mb_substr($author->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-800">{{ $author->name }}</span>
                                    <span class="mx-1">|</span>
                                    @if($prediction->published_at)
                                        {{ $prediction->published_at->format('d/m/Y H:i') }}
                                    @else
                                        {{ $prediction->created_at->format('d/m/Y H:i') }}
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Analysis intro paragraph --}}
                        <p class="text-sm text-gray-700 mb-4">
                            Dựa trên kết quả xổ số Miền Bắc ngày <strong>{{ $refDateFormatted }}</strong>
                            @if($prevSpecialPrize)
                                với giải Đặc Biệt <strong class="text-red-600">{{ $prevSpecialPrize }}</strong>,
                            @endif
                            chúng tôi phân tích và đưa ra các con số dự đoán cho ngày <strong>{{ $formattedDate }}</strong>.
                            Các số liệu được tính toán bằng <strong>thuật toán thống kê</strong> kết hợp phân tích <strong>tần suất</strong> và <strong>lô gan</strong>.
                        </p>

                        {{-- H2: Giải mã dự đoán --}}
                        <h2 class="text-lg font-bold text-gray-900 mb-4">
                            📊 Giải mã Dự đoán kết quả XSMB {{ $weekdayName }} - Ngày {{ $formattedDate }}
                        </h2>

                        {{-- Reference text + lottery results table --}}
                        <p class="text-sm text-gray-700 mb-2">
                            Bảng kết quả xổ số Miền Bắc ngày {{ $refDateFormatted }} (ngày tham chiếu):
                        </p>

                        @include('predictions.partials.lottery-results-table', [
                            'results' => $prediction->lottery_results_snapshot,
                            'regionSlug' => $regionSlug,
                            'referenceDate' => $prediction->reference_date
                        ])

                        {{-- Prediction numbers intro --}}
                        <p class="text-sm text-gray-700 mb-2">
                            Bảng dự đoán các con số may mắn cho kỳ quay ngày {{ $formattedDate }}:
                        </p>

                        {{-- Prediction Numbers --}}
                        @include('predictions.partials.prediction-numbers', [
                            'predictionsData' => $prediction->predictions_data,
                            'regionSlug' => $regionSlug,
                            'date' => $prediction->prediction_date
                        ])

                        {{-- Methodology paragraph --}}
                        <p class="text-sm text-gray-700 mb-2">
                            Phương pháp soi cầu dựa trên phân tích <strong>bạch thủ</strong>, <strong>cầu 2 nháy</strong>,
                            <strong>tam giác Pascal</strong> và <strong>lô kẹp</strong> từ các kỳ quay trước.
                        </p>

                        {{-- Beautiful Numbers --}}
                        @include('predictions.partials.beautiful-numbers', [
                            'analysisData' => $prediction->analysis_data,
                            'regionSlug' => $regionSlug,
                            'date' => $prediction->prediction_date
                        ])

                        {{-- Internal links paragraph --}}
                        <p class="text-sm text-gray-700 mb-4">
                            Tham khảo thêm các công cụ phân tích:
                            <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">thống kê xổ số</a>,
                            <a href="{{ route('statistics.overdue') }}" class="text-[#0066cc] hover:underline">lô gan XSMB</a>,
                            <a href="{{ route('statistics.frequency') }}" class="text-[#0066cc] hover:underline">tần suất lô tô</a>.
                        </p>

                        {{-- Statistics Tables --}}
                        @include('predictions.partials.statistics-tables', [
                            'statistics' => $prediction->statistics_snapshot,
                            'last10SpecialPrizes' => $last10SpecialPrizes,
                            'last30SpecialPrizes' => $last30SpecialPrizes,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Trial Draw Section (INLINE) --}}
                        <div class="my-6 bg-white border border-gray-300 overflow-hidden">
                            <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
                                <h2 class="text-lg font-bold text-gray-900">
                                    🎰 Quay thử XSMB ngày {{ $formattedDate }}
                                </h2>
                            </div>
                            <div class="p-4">
                                <p class="text-sm text-gray-700 mb-3">
                                    Thử vận may của bạn với công cụ quay thử xổ số miễn phí. Xem kết quả ngay lập tức!
                                </p>
                                <a href="{{ $trialDrawUrl }}" class="text-[#0066cc] hover:underline font-medium">
                                    👉 Quay thử Miền Bắc ngay
                                </a>
                            </div>
                        </div>

                        {{-- FAQ Section --}}
                        @include('predictions.partials.faq-section', [
                            'regionSlug' => $regionSlug,
                            'regionName' => $regionName,
                            'date' => $prediction->prediction_date
                        ])

                        {{-- "Tham khảo thêm:" bullet links (replaces related-predictions) --}}
                        @if(!empty($relatedPredictions))
                        <div class="my-6">
                            <h3 class="text-base font-bold text-gray-900 mb-2">📌 Tham khảo thêm:</h3>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach($relatedPredictions as $related)
                                    <li>
                                        <a href="{{ $related->url }}" class="text-[#0066cc] hover:underline">
                                            Dự đoán {{ $related->region_name }} ngày {{ $related->formatted_date }}
                                        </a>
                                    </li>
                                @endforeach
                                <li>
                                    <a href="{{ route('prediction.index') }}" class="text-[#0066cc] hover:underline">
                                        Dự đoán xổ số 3 miền hôm nay
                                    </a>
                                </li>
                            </ul>
                        </div>
                        @endif

                        {{-- "NHIỀU NGƯỜI ĐANG XEM:" yellow box --}}
                        <div class="my-6 bg-yellow-50 border border-yellow-300 p-4">
                            <h3 class="text-base font-bold text-gray-900 mb-2">🔥 NHIỀU NGƯỜI ĐANG XEM:</h3>
                            <ul class="list-disc list-inside text-sm space-y-1">
                                <li>
                                    <a href="{{ route('prediction.xsmb.index') }}" class="text-[#0066cc] hover:underline">
                                        Soi cầu XSMB - Dự đoán Miền Bắc hôm nay
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('prediction.xsmn.index') }}" class="text-[#0066cc] hover:underline">
                                        Soi cầu XSMN - Dự đoán Miền Nam hôm nay
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('prediction.xsmt.index') }}" class="text-[#0066cc] hover:underline">
                                        Soi cầu XSMT - Dự đoán Miền Trung hôm nay
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('statistics') }}" class="text-[#0066cc] hover:underline">
                                        Thống kê xổ số Miền Bắc
                                    </a>
                                </li>
                            </ul>
                        </div>

                        {{-- Red italic disclaimer --}}
                        <p class="text-sm text-red-600 italic">
                            * Lưu ý: Kết quả soi cầu chỉ mang tính chất tham khảo, dựa trên phân tích thống kê.
                            Xổ số là trò chơi may rủi, kết quả hoàn toàn ngẫu nhiên.
                        </p>

                    </div>
                </div>

                {{-- Previous/Next Navigation --}}
                <div class="flex justify-between items-center mb-4">
                    @if($previousPrediction)
                        <a href="{{ $previousPrediction->url }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ $previousPrediction->formatted_date }}
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextPrediction)
                        <a href="{{ $nextPrediction->url }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 transition-colors text-sm">
                            {{ $nextPrediction->formatted_date }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <div></div>
                    @endif
                </div>
            </div>

            {{-- Hub Sidebar --}}
            @include('predictions.partials.hub.sidebar')
        </div>

    @else
        {{-- ========== XSMN/XSMT BRANCH (Unchanged layout, but with hub sidebar + tabs) ========== --}}

        {{-- Two-Column Layout --}}
        <div class="flex flex-col lg:flex-row gap-4">

            {{-- Main Content Column --}}
            <div class="flex-1 min-w-0">

                <article class="bg-white border border-gray-200 overflow-hidden mb-4">
                    <div class="p-4 md:p-6">
                        {{-- Title --}}
                        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                            Soi cầu KQ{{ strtoupper($regionSlug) }} {{ $prediction->formatted_date }} - Dự đoán xổ số {{ $regionName }}
                        </h1>

                        {{-- Meta Info --}}
                        @include('predictions.partials.author-info', ['author' => $author, 'prediction' => $prediction])

                        {{-- XSMN/XSMT: Multi-Province Layout --}}

                        {{-- Introduction Paragraph with Province List --}}
                        @include('predictions.partials.xsmn.intro-paragraph', [
                            'provinces' => $provinces,
                            'analysisData' => $prediction->analysis_data,
                            'date' => $prediction->prediction_date,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Multi-Province Lottery Results Table --}}
                        @include('predictions.partials.xsmn.multi-province-results', [
                            'provinces' => $provinces,
                            'results' => $prediction->lottery_results_snapshot,
                            'referenceDate' => $prediction->reference_date,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Region Aggregate Predictions (DAC BIET 4 DAI) --}}
                        @include('predictions.partials.xsmn.region-predictions', [
                            'provinces' => $provinces,
                            'predictionsData' => $prediction->predictions_data,
                            'date' => $prediction->prediction_date,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Per-Province VIP Sections --}}
                        @include('predictions.partials.xsmn.province-vip-section', [
                            'provinces' => $provinces,
                            'predictionsData' => $prediction->predictions_data,
                            'date' => $prediction->prediction_date,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Per-Province Analysis Tables --}}
                        @include('predictions.partials.xsmn.province-analysis', [
                            'provinces' => $provinces,
                            'analysisData' => $prediction->analysis_data,
                            'date' => $prediction->prediction_date,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Statistics Tables with Progress Bars --}}
                        @include('predictions.partials.xsmn.statistics-tables', [
                            'provinces' => $provinces,
                            'statistics' => $prediction->statistics_snapshot,
                            'last10SpecialPrizes' => $last10SpecialPrizes,
                            'last30SpecialPrizes' => $last30SpecialPrizes,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Trial Draw Section --}}
                        @include('predictions.partials.xsmn.trial-draw', [
                            'provinces' => $provinces,
                            'trialDrawUrl' => $trialDrawUrl,
                            'date' => $prediction->prediction_date,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- XSMN-specific FAQ Section --}}
                        @include('predictions.partials.xsmn.faq-section', [
                            'provinces' => $provinces,
                            'regionSlug' => $regionSlug,
                            'regionName' => $regionName
                        ])
                    </div>
                </article>

                {{-- Navigation --}}
                <div class="flex justify-between items-center mb-4">
                    @if($previousPrediction)
                        <a href="{{ $previousPrediction->url }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 transition-colors text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ $previousPrediction->formatted_date }}
                        </a>
                    @else
                        <div></div>
                    @endif

                    @if($nextPrediction)
                        <a href="{{ $nextPrediction->url }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 transition-colors text-sm">
                            {{ $nextPrediction->formatted_date }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    @else
                        <div></div>
                    @endif
                </div>

                {{-- Related Predictions --}}
                @include('predictions.partials.related-predictions', ['relatedPredictions' => $relatedPredictions])
            </div>

            {{-- Hub Sidebar --}}
            @include('predictions.partials.hub.sidebar')
        </div>
    @endif
</div>
@endsection
