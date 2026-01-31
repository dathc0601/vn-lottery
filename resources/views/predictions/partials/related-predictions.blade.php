@if(!empty($relatedPredictions))
<div class="mb-4">
    <div class="bg-white rounded shadow overflow-hidden">
        <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
            Dự đoán các khu vực khác
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($relatedPredictions as $related)
                    <a href="{{ $related->url }}"
                       class="block p-4 border border-gray-200 rounded-lg hover:border-[#ff6600] hover:shadow transition-all">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="inline-block text-xs text-white bg-blue-500 px-2 py-0.5 rounded mb-1">
                                    {{ strtoupper($related->region_slug) }}
                                </span>
                                <h4 class="font-semibold text-gray-900">
                                    Dự đoán {{ $related->region_name }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    {{ $related->formatted_date }}
                                </p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
