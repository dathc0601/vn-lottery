<div class="flex gap-4 py-4 border-b border-gray-200">
    {{-- Thumbnail --}}
    <a href="{{ $prediction->url }}" class="flex-shrink-0">
        @include('predictions.partials.thumbnail', [
            'thumbSlug' => $prediction->region_slug,
            'thumbDate' => $prediction->formatted_date,
        ])
    </a>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <h3 class="text-base font-semibold mb-1">
            <a href="{{ $prediction->url }}"
               class="text-[#0066cc] hover:text-[#cc0000] transition-colors">
                Soi cầu KQ{{ strtoupper($prediction->region_slug) }} {{ $prediction->formatted_date }} - Dự đoán xổ số {{ $prediction->region_name }}
            </a>
        </h3>
        <p class="text-sm text-gray-600 line-clamp-3">
            Dự đoán kết quả {{ strtoupper($prediction->region_slug) }} ngày {{ $prediction->formatted_date }}.
            Phân tích thống kê, soi cầu lô đề {{ $prediction->region_name }} chính xác nhất.
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
