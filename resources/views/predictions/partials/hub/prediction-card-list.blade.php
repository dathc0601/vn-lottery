@php
    use App\Models\Prediction;

    $regionCards = [
        Prediction::REGION_NORTH => [
            'slug' => 'xsmb',
            'label' => 'XSMB',
            'title' => 'Dự đoán xổ số Miền Bắc',
            'image' => 'xsmb.svg',
        ],
        Prediction::REGION_CENTRAL => [
            'slug' => 'xsmt',
            'label' => 'XSMT',
            'title' => 'Dự đoán xổ số Miền Trung',
            'image' => 'xsmt.svg',
        ],
        Prediction::REGION_SOUTH => [
            'slug' => 'xsmn',
            'label' => 'XSMN',
            'title' => 'Dự đoán xổ số Miền Nam',
            'image' => 'xsmn.svg',
        ],
    ];
@endphp

<div>
    @foreach($regionCards as $region => $card)
        @php $prediction = $latestByRegion[$region] ?? null; @endphp
        <div class="flex gap-4 py-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
            {{-- Thumbnail --}}
            <a href="{{ $prediction ? $prediction->url : route('prediction.' . $card['slug'] . '.index') }}"
               class="flex-shrink-0">
                @include('predictions.partials.thumbnail', [
                    'thumbSlug' => $card['slug'],
                    'thumbDate' => $prediction ? $prediction->formatted_date : null,
                ])
            </a>

            {{-- Content --}}
            <div class="flex-1 min-w-0">
                <h3 class="text-base font-semibold mb-1">
                    <a href="{{ $prediction ? $prediction->url : route('prediction.' . $card['slug'] . '.index') }}"
                       class="text-[#0066cc] hover:text-[#cc0000] transition-colors">
                        {{ $card['title'] }}
                        @if($prediction)
                            - {{ $prediction->formatted_date }}
                        @endif
                    </a>
                </h3>
                @if($prediction)
                    <p class="text-sm text-gray-600 line-clamp-3">
                        Dự đoán kết quả {{ $card['label'] }} ngày {{ $prediction->formatted_date }}.
                        Phân tích thống kê, soi cầu lô đề {{ Prediction::REGIONS[$region] }} chính xác nhất.
                        @php $loto = $prediction->predictions_data['loto_2_digit'] ?? []; @endphp
                        @if(!empty($loto))
                            Các cặp loto dự đoán: {{ implode(', ', array_slice($loto, 0, 5)) }}.
                        @endif
                    </p>
                @else
                    <p class="text-sm text-gray-500">Chưa có dự đoán cho khu vực này.</p>
                @endif
                <a href="{{ route('prediction.' . $card['slug'] . '.index') }}"
                   class="inline-block mt-2 text-sm text-[#0066cc] hover:text-[#cc0000]">
                    Xem thêm &raquo;
                </a>
            </div>
        </div>
    @endforeach
</div>
