<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class PredictionArticleService
{
    protected PredictionService $predictionService;

    public function __construct(PredictionService $predictionService)
    {
        $this->predictionService = $predictionService;
    }

    /**
     * Generate article for a prediction.
     */
    public function generateArticle(Prediction $prediction): ?Article
    {
        $regionNames = [
            'north' => 'Miền Bắc',
            'central' => 'Miền Trung',
            'south' => 'Miền Nam',
        ];

        $regionCodes = [
            'north' => 'XSMB',
            'central' => 'XSMT',
            'south' => 'XSMN',
        ];

        $regionName = $regionNames[$prediction->region] ?? $prediction->region;
        $regionCode = $regionCodes[$prediction->region] ?? strtoupper($prediction->region);
        $formattedDate = $prediction->prediction_date->format('d/m/Y');

        // Get admin user for author
        $author = $this->getAdminUser();
        if (!$author) {
            Log::error('No admin user found for prediction article');
            return null;
        }

        // Get or create prediction category
        $category = $this->getPredictionCategory();

        // Generate title
        $title = "Soi cầu KQ{$regionCode} {$formattedDate} - Dự đoán xổ số {$regionName}";

        // Generate slug
        $slug = Str::slug("soi-cau-{$regionCode}-{$prediction->prediction_date->format('d-m-Y')}");

        // Generate excerpt
        $excerpt = "Dự đoán kết quả xổ số {$regionName} ngày {$formattedDate}. " .
            "Phân tích thống kê, soi cầu lô đề chính xác nhất hôm nay.";

        // Generate content
        $content = $this->generateArticleContent($prediction);

        // Create or update article
        $article = Article::updateOrCreate(
            ['slug' => $slug],
            [
                'title' => $title,
                'excerpt' => $excerpt,
                'content' => $content,
                'author_id' => $author->id,
                'category_id' => $category?->id,
                'status' => 'published',
                'is_featured' => false,
                'published_at' => now(),
                'meta_title' => $title,
                'meta_description' => $excerpt,
                'meta_keywords' => "soi cầu {$regionCode}, dự đoán {$regionCode}, xổ số {$regionName}, " .
                    "kết quả {$regionCode} {$formattedDate}",
            ]
        );

        // Link article to prediction
        $prediction->update(['article_id' => $article->id]);

        Log::info("Generated article for prediction: {$prediction->id}, article: {$article->id}");

        return $article;
    }

    /**
     * Get admin user for author attribution.
     */
    protected function getAdminUser(): ?User
    {
        return User::where('is_admin', true)->first();
    }

    /**
     * Get or create prediction category.
     */
    protected function getPredictionCategory(): ?ArticleCategory
    {
        return ArticleCategory::firstOrCreate(
            ['slug' => 'du-doan'],
            [
                'name' => 'Dự đoán xổ số',
                'description' => 'Các bài dự đoán và soi cầu xổ số hàng ngày',
                'is_active' => true,
                'sort_order' => 10,
            ]
        );
    }

    /**
     * Generate article content HTML.
     */
    protected function generateArticleContent(Prediction $prediction): string
    {
        $regionNames = [
            'north' => 'Miền Bắc',
            'central' => 'Miền Trung',
            'south' => 'Miền Nam',
        ];

        $regionCodes = [
            'north' => 'XSMB',
            'central' => 'XSMT',
            'south' => 'XSMN',
        ];

        $trialDrawUrls = [
            'north' => '/quay-thu-xsmb',
            'central' => '/quay-thu-xsmt',
            'south' => '/quay-thu-xsmn',
        ];

        $regionName = $regionNames[$prediction->region] ?? $prediction->region;
        $regionCode = $regionCodes[$prediction->region] ?? strtoupper($prediction->region);
        $formattedDate = $prediction->prediction_date->format('d/m/Y');
        $trialDrawUrl = $trialDrawUrls[$prediction->region] ?? '/quay-thu-xo-so-hom-nay';

        $predictionsData = $prediction->predictions_data ?? [];
        $analysisData = $prediction->analysis_data ?? [];
        $lotteryResults = $prediction->lottery_results_snapshot ?? [];
        $statistics = $prediction->statistics_snapshot ?? [];

        // Build content sections
        $content = $this->buildIntroSection($regionCode, $regionName, $formattedDate);
        $content .= $this->buildLotteryResultsSection($lotteryResults, $regionCode, $prediction->reference_date);
        $content .= $this->buildPredictionNumbersSection($predictionsData, $regionCode);
        $content .= $this->buildAnalysisSection($analysisData, $regionCode);
        $content .= $this->buildStatisticsSection($statistics, $prediction->region);
        $content .= $this->buildTrialDrawSection($trialDrawUrl, $regionCode);
        $content .= $this->buildFaqSection($regionCode, $regionName);

        return $content;
    }

    /**
     * Build intro section.
     */
    protected function buildIntroSection(string $regionCode, string $regionName, string $date): string
    {
        return <<<HTML
<div class="prediction-intro">
    <p><strong>Soi cầu {$regionCode} ngày {$date}</strong> - Chuyên trang phân tích và dự đoán kết quả xổ số {$regionName} dựa trên các thuật toán thống kê hiện đại. Cập nhật mỗi ngày vào lúc 2h sáng.</p>
</div>
HTML;
    }

    /**
     * Build lottery results section.
     */
    protected function buildLotteryResultsSection(array $results, string $regionCode, $referenceDate): string
    {
        if (empty($results)) {
            return '';
        }

        $dateFormatted = $referenceDate instanceof \Carbon\Carbon
            ? $referenceDate->format('d/m/Y')
            : date('d/m/Y', strtotime($referenceDate));

        $tableRows = '';
        foreach ($results as $result) {
            $provinceName = $result['province_name'] ?? '';
            $tableRows .= "<tr>";
            $tableRows .= "<td><strong>{$provinceName}</strong></td>";
            $tableRows .= "<td class='text-red-600 font-bold'>{$result['prize_special']}</td>";
            $tableRows .= "<td>{$result['prize_1']}</td>";
            $tableRows .= "<td>{$result['prize_2']}</td>";
            $tableRows .= "<td>{$result['prize_3']}</td>";
            $tableRows .= "<td>{$result['prize_4']}</td>";
            $tableRows .= "<td>{$result['prize_5']}</td>";
            $tableRows .= "<td>{$result['prize_6']}</td>";
            $tableRows .= "<td>{$result['prize_7']}</td>";
            if (isset($result['prize_8'])) {
                $tableRows .= "<td>{$result['prize_8']}</td>";
            }
            $tableRows .= "</tr>";
        }

        return <<<HTML
<div class="lottery-results-section my-6">
    <h2 class="text-xl font-bold mb-4">Kết quả {$regionCode} ngày {$dateFormatted}</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border border-gray-300 px-2 py-1">Tỉnh</th>
                    <th class="border border-gray-300 px-2 py-1">ĐB</th>
                    <th class="border border-gray-300 px-2 py-1">G1</th>
                    <th class="border border-gray-300 px-2 py-1">G2</th>
                    <th class="border border-gray-300 px-2 py-1">G3</th>
                    <th class="border border-gray-300 px-2 py-1">G4</th>
                    <th class="border border-gray-300 px-2 py-1">G5</th>
                    <th class="border border-gray-300 px-2 py-1">G6</th>
                    <th class="border border-gray-300 px-2 py-1">G7</th>
                    <th class="border border-gray-300 px-2 py-1">G8</th>
                </tr>
            </thead>
            <tbody>
                {$tableRows}
            </tbody>
        </table>
    </div>
</div>
HTML;
    }

    /**
     * Build prediction numbers section.
     */
    protected function buildPredictionNumbersSection(array $data, string $regionCode): string
    {
        $headTail = $data['head_tail'] ?? [];
        $loto2Digit = $data['loto_2_digit'] ?? [];
        $loto3Digit = $data['loto_3_digit'] ?? [];
        $vip4Digit = $data['vip_4_digit'] ?? [];

        $headTailHtml = $this->formatNumberBox(
            'Đầu đuôi giải ĐB',
            $headTail['combined'] ?? [],
            'bg-orange-100 border-orange-500'
        );

        $loto2Html = $this->formatNumberBox(
            'Loto 2 số hay về',
            $loto2Digit,
            'bg-blue-100 border-blue-500'
        );

        $loto3Html = $this->formatNumberBox(
            'Lô tô 3 số - 3 càng đẹp',
            $loto3Digit,
            'bg-green-100 border-green-500'
        );

        $vip4Html = $this->formatNumberBox(
            'Soi cầu 4 số VIP',
            $vip4Digit,
            'bg-purple-100 border-purple-500'
        );

        return <<<HTML
<div class="prediction-numbers-section my-6">
    <h2 class="text-xl font-bold mb-4">Dự đoán {$regionCode} hôm nay</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {$headTailHtml}
        {$loto2Html}
        {$loto3Html}
        {$vip4Html}
    </div>
</div>
HTML;
    }

    /**
     * Format a number box component.
     */
    protected function formatNumberBox(string $title, array $numbers, string $classes): string
    {
        $numbersHtml = '';
        foreach ($numbers as $num) {
            $numbersHtml .= "<span class='inline-block px-3 py-1 m-1 bg-white rounded font-bold text-lg'>{$num}</span>";
        }

        return <<<HTML
<div class="p-4 rounded-lg border-2 {$classes}">
    <h3 class="font-semibold mb-2">{$title}</h3>
    <div class="flex flex-wrap">
        {$numbersHtml}
    </div>
</div>
HTML;
    }

    /**
     * Build analysis section (beautiful numbers).
     */
    protected function buildAnalysisSection(array $data, string $regionCode): string
    {
        $sections = [
            ['key' => 'bach_thu', 'title' => 'Bạch thủ (số lâu chưa về)', 'color' => 'red'],
            ['key' => 'lat_lien_tuc', 'title' => 'Lật liên tục', 'color' => 'yellow'],
            ['key' => 'cau_2_nhay', 'title' => 'Cầu 2 nháy', 'color' => 'green'],
            ['key' => 'pascal_triangle', 'title' => 'Tam giác Pascal', 'color' => 'blue'],
            ['key' => 'lo_kep', 'title' => 'Cầu lô kẹp', 'color' => 'indigo'],
            ['key' => 'loto_hay_ve', 'title' => 'Lô tô hay về (30 kỳ)', 'color' => 'pink'],
        ];

        $itemsHtml = '';
        foreach ($sections as $section) {
            $numbers = $data[$section['key']] ?? [];
            if (empty($numbers)) {
                continue;
            }

            $numbersStr = implode(', ', array_map(fn($n) => "<strong>{$n}</strong>", $numbers));
            $itemsHtml .= <<<HTML
<div class="p-3 bg-{$section['color']}-50 rounded border border-{$section['color']}-200">
    <h4 class="font-semibold text-{$section['color']}-700">{$section['title']}</h4>
    <p class="mt-1">{$numbersStr}</p>
</div>
HTML;
        }

        return <<<HTML
<div class="analysis-section my-6">
    <h2 class="text-xl font-bold mb-4">Phân tích số đẹp {$regionCode}</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {$itemsHtml}
    </div>
</div>
HTML;
    }

    /**
     * Build statistics section.
     */
    protected function buildStatisticsSection(array $statistics, string $region): string
    {
        $topFrequency = $statistics['top_frequency_30d'] ?? [];
        $topGap = $statistics['top_gap'] ?? [];

        $frequencyHtml = '';
        $count = 0;
        foreach ($topFrequency as $num => $freq) {
            if ($count++ >= 10) break;
            $frequencyHtml .= "<tr><td class='border px-2 py-1'>{$num}</td><td class='border px-2 py-1'>{$freq}</td></tr>";
        }

        $gapHtml = '';
        $count = 0;
        foreach ($topGap as $num => $gap) {
            if ($count++ >= 10) break;
            $gapHtml .= "<tr><td class='border px-2 py-1'>{$num}</td><td class='border px-2 py-1'>{$gap} ngày</td></tr>";
        }

        return <<<HTML
<div class="statistics-section my-6">
    <h2 class="text-xl font-bold mb-4">Thống kê tần suất</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="font-semibold mb-2">Top 10 số về nhiều nhất (30 ngày)</h3>
            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Số</th>
                        <th class="border px-2 py-1">Tần suất</th>
                    </tr>
                </thead>
                <tbody>
                    {$frequencyHtml}
                </tbody>
            </table>
        </div>
        <div>
            <h3 class="font-semibold mb-2">Top 10 số lâu chưa về</h3>
            <table class="w-full border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1">Số</th>
                        <th class="border px-2 py-1">Số ngày</th>
                    </tr>
                </thead>
                <tbody>
                    {$gapHtml}
                </tbody>
            </table>
        </div>
    </div>
</div>
HTML;
    }

    /**
     * Build trial draw section.
     */
    protected function buildTrialDrawSection(string $url, string $regionCode): string
    {
        return <<<HTML
<div class="trial-draw-section my-6 p-4 bg-gradient-to-r from-orange-100 to-yellow-100 rounded-lg">
    <h2 class="text-xl font-bold mb-2">Quay thử {$regionCode}</h2>
    <p class="mb-3">Thử vận may của bạn với công cụ quay thử xổ số miễn phí.</p>
    <a href="{$url}" class="inline-block px-6 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
        Quay thử ngay
    </a>
</div>
HTML;
    }

    /**
     * Build FAQ section with JSON-LD schema.
     */
    protected function buildFaqSection(string $regionCode, string $regionName): string
    {
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

        $faqItemsHtml = '';
        $jsonLdItems = [];

        foreach ($faqs as $faq) {
            $faqItemsHtml .= <<<HTML
<div class="border-b border-gray-200 py-3">
    <h3 class="font-semibold text-gray-800">{$faq['question']}</h3>
    <p class="mt-2 text-gray-600">{$faq['answer']}</p>
</div>
HTML;

            $jsonLdItems[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        $jsonLd = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $jsonLdItems,
        ], JSON_UNESCAPED_UNICODE);

        return <<<HTML
<div class="faq-section my-6">
    <h2 class="text-xl font-bold mb-4">Câu hỏi thường gặp</h2>
    <div class="space-y-2">
        {$faqItemsHtml}
    </div>
</div>
<script type="application/ld+json">
{$jsonLd}
</script>
HTML;
    }
}
