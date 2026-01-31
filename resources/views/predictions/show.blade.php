@extends('layouts.app')

@section('title', "Soi cầu KQ" . strtoupper($regionSlug) . " " . $prediction->formatted_date . " - Dự đoán xổ số " . $regionName)

@section('meta_description', "Dự đoán kết quả xổ số {$regionName} ngày {$prediction->formatted_date}. Phân tích thống kê, soi cầu lô đề chính xác nhất hôm nay.")

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('prediction.' . $regionSlug . '.index') }}" class="text-[#0066cc] hover:underline">Dự đoán {{ strtoupper($regionSlug) }}</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">{{ $prediction->formatted_date }}</span>
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
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <article class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="p-4 md:p-6">
                    {{-- Title --}}
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                        Soi cầu KQ{{ strtoupper($regionSlug) }} {{ $prediction->formatted_date }} - Dự đoán xổ số {{ $regionName }}
                    </h1>

                    {{-- Meta Info --}}
                    @include('predictions.partials.author-info', ['author' => $author, 'prediction' => $prediction])

                    @if($regionSlug === 'xsmn' || $regionSlug === 'xsmt')
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

                    @else
                        {{-- XSMB: Original Single-Province Layout --}}

                        {{-- Introduction --}}
                        <div class="text-lg text-gray-700 mb-6 font-medium italic border-l-4 border-[#ff6600] pl-4">
                            Soi cầu {{ strtoupper($regionSlug) }} ngày {{ $prediction->formatted_date }} - Chuyên trang phân tích và dự đoán kết quả xổ số {{ $regionName }}
                            dựa trên các thuật toán thống kê hiện đại. Cập nhật mỗi ngày vào lúc 2h sáng.
                        </div>

                        {{-- Previous Day's Lottery Results --}}
                        @include('predictions.partials.lottery-results-table', [
                            'results' => $prediction->lottery_results_snapshot,
                            'regionSlug' => $regionSlug,
                            'referenceDate' => $prediction->reference_date
                        ])

                        {{-- Prediction Numbers --}}
                        @include('predictions.partials.prediction-numbers', [
                            'predictionsData' => $prediction->predictions_data,
                            'regionSlug' => $regionSlug,
                            'date' => $prediction->prediction_date
                        ])

                        {{-- Beautiful Numbers Analysis --}}
                        @include('predictions.partials.beautiful-numbers', [
                            'analysisData' => $prediction->analysis_data,
                            'regionSlug' => $regionSlug,
                            'date' => $prediction->prediction_date
                        ])

                        {{-- Statistics Tables --}}
                        @include('predictions.partials.statistics-tables', [
                            'statistics' => $prediction->statistics_snapshot,
                            'last10SpecialPrizes' => $last10SpecialPrizes,
                            'last30SpecialPrizes' => $last30SpecialPrizes,
                            'regionSlug' => $regionSlug
                        ])

                        {{-- Trial Draw Link --}}
                        @include('predictions.partials.trial-draw-link', [
                            'trialDrawUrl' => $trialDrawUrl,
                            'regionSlug' => $regionSlug,
                            'date' => $prediction->prediction_date
                        ])

                        {{-- FAQ Section --}}
                        @include('predictions.partials.faq-section', [
                            'regionSlug' => $regionSlug,
                            'regionName' => $regionName
                        ])

                    @endif

                    {{-- Share Section --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-700">Chia sẻ:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-8 h-8 bg-blue-600 text-white rounded flex items-center justify-center hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode('Soi cầu ' . strtoupper($regionSlug) . ' ' . $prediction->formatted_date) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-8 h-8 bg-black text-white rounded flex items-center justify-center hover:bg-gray-800 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode('Soi cầu ' . strtoupper($regionSlug) . ' ' . $prediction->formatted_date) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-8 h-8 bg-sky-500 text-white rounded flex items-center justify-center hover:bg-sky-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Navigation --}}
            <div class="flex justify-between items-center mb-4">
                @if($previousPrediction)
                    <a href="{{ $previousPrediction->url }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded shadow hover:bg-gray-50 transition-colors">
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
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded shadow hover:bg-gray-50 transition-colors">
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

            {{-- Back to List --}}
            <div class="text-center">
                <a href="{{ route('prediction.' . $regionSlug . '.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Quay lại danh sách
                </a>
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
