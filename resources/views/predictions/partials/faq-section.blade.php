@php
    $regionCode = strtoupper($regionSlug);
    $faqs = [
        [
            'question' => "Soi cầu {$regionCode} là gì?",
            'answer' => "Soi cầu {$regionCode} là phương pháp phân tích kết quả xổ số {$regionName} dựa trên các thuật toán thống kê để dự đoán các con số có khả năng xuất hiện cao trong các kỳ quay tiếp theo."
        ],
        [
            'question' => "Làm thế nào để sử dụng dự đoán {$regionCode}?",
            'answer' => "Bạn có thể tham khảo các số dự đoán được cung cấp trên trang này, bao gồm đầu đuôi giải đặc biệt, loto 2-3-4 số, và các phân tích thống kê chi tiết."
        ],
        [
            'question' => "Dự đoán {$regionCode} được cập nhật khi nào?",
            'answer' => "Dự đoán {$regionCode} được tự động cập nhật hàng ngày vào lúc 2h sáng, dựa trên kết quả xổ số ngày hôm trước."
        ],
        [
            'question' => "Độ chính xác của soi cầu {$regionCode}?",
            'answer' => "Soi cầu {$regionCode} dựa trên phân tích thống kê và chỉ mang tính chất tham khảo. Kết quả xổ số hoàn toàn ngẫu nhiên và không thể dự đoán chính xác 100%."
        ],
    ];

    $jsonLdItems = [];
    foreach ($faqs as $faq) {
        $jsonLdItems[] = [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => $faq['answer'],
            ],
        ];
    }

    $faqSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $jsonLdItems,
    ];
@endphp

<div class="my-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Câu hỏi thường gặp
    </h2>

    <div class="bg-white rounded shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
            @foreach($faqs as $index => $faq)
            <details class="group" {{ $index === 0 ? 'open' : '' }}>
                <summary class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50 transition-colors">
                    <h3 class="font-semibold text-gray-800 pr-4">{{ $faq['question'] }}</h3>
                    <svg class="w-5 h-5 text-gray-500 transform group-open:rotate-180 transition-transform flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="px-4 pb-4 text-gray-600">
                    {{ $faq['answer'] }}
                </div>
            </details>
            @endforeach
        </div>
    </div>
</div>

{{-- JSON-LD Schema for FAQPage --}}
<script type="application/ld+json">
    @json($faqSchema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
</script>
