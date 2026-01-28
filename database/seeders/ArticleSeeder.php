<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('is_admin', true)->first();
        $authorId = $author?->id;

        $articles = [
            // Tin xổ số
            [
                'category_slug' => 'tin-xo-so',
                'title' => 'Kết quả xổ số miền Bắc hôm nay - Cập nhật nhanh nhất',
                'slug' => 'ket-qua-xo-so-mien-bac-hom-nay',
                'excerpt' => 'Cập nhật kết quả xổ số miền Bắc (XSMB) hôm nay nhanh và chính xác nhất. Xem ngay bảng kết quả đầy đủ các giải từ đặc biệt đến giải 7.',
                'content' => $this->getArticleContent1(),
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'view_count' => 1520,
            ],
            [
                'category_slug' => 'tin-xo-so',
                'title' => 'Lịch quay xổ số các tỉnh miền Nam năm 2026',
                'slug' => 'lich-quay-xo-so-cac-tinh-mien-nam-2026',
                'excerpt' => 'Tổng hợp lịch quay xổ số các tỉnh miền Nam chi tiết theo từng ngày trong tuần năm 2026.',
                'content' => $this->getArticleContent2(),
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'view_count' => 890,
            ],

            // Soi cầu - Dự đoán
            [
                'category_slug' => 'soi-cau-du-doan',
                'title' => 'Phương pháp soi cầu lô đề hiệu quả nhất 2026',
                'slug' => 'phuong-phap-soi-cau-lo-de-hieu-qua-2026',
                'excerpt' => 'Tổng hợp các phương pháp soi cầu lô đề được nhiều người áp dụng và có tỷ lệ thành công cao nhất.',
                'content' => $this->getArticleContent3(),
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'view_count' => 2340,
            ],
            [
                'category_slug' => 'soi-cau-du-doan',
                'title' => 'Thống kê lô gan miền Bắc - Những con số đáng chú ý',
                'slug' => 'thong-ke-lo-gan-mien-bac-nhung-con-so-dang-chu-y',
                'excerpt' => 'Phân tích chi tiết các con số lô gan miền Bắc và dự đoán thời điểm chúng có thể xuất hiện trở lại.',
                'content' => $this->getArticleContent4(),
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(4),
                'view_count' => 1876,
            ],

            // Kiến thức xổ số
            [
                'category_slug' => 'kien-thuc-xo-so',
                'title' => 'Hướng dẫn cách chơi xổ số cho người mới bắt đầu',
                'slug' => 'huong-dan-cach-choi-xo-so-cho-nguoi-moi',
                'excerpt' => 'Bài viết hướng dẫn chi tiết cách chơi xổ số truyền thống và xổ số Vietlott dành cho người mới.',
                'content' => $this->getArticleContent5(),
                'is_featured' => true,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'view_count' => 3210,
            ],
            [
                'category_slug' => 'kien-thuc-xo-so',
                'title' => 'Cơ cấu giải thưởng xổ số các miền - So sánh chi tiết',
                'slug' => 'co-cau-giai-thuong-xo-so-cac-mien-so-sanh',
                'excerpt' => 'So sánh chi tiết cơ cấu giải thưởng xổ số miền Bắc, miền Trung và miền Nam.',
                'content' => $this->getArticleContent6(),
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(7),
                'view_count' => 1456,
            ],

            // Người trúng giải
            [
                'category_slug' => 'nguoi-trung-giai',
                'title' => 'Câu chuyện người trúng Jackpot 150 tỷ đổi đời',
                'slug' => 'cau-chuyen-nguoi-trung-jackpot-150-ty-doi-doi',
                'excerpt' => 'Chia sẻ câu chuyện thực tế về một người may mắn trúng giải Jackpot Vietlott và cuộc sống sau đó.',
                'content' => $this->getArticleContent7(),
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'view_count' => 4560,
            ],

            // Vietlott
            [
                'category_slug' => 'vietlott',
                'title' => 'Hướng dẫn chơi Power 6/55 - Cơ hội trúng Jackpot',
                'slug' => 'huong-dan-choi-power-655-co-hoi-trung-jackpot',
                'excerpt' => 'Tìm hiểu cách chơi xổ số Power 6/55 của Vietlott, cơ cấu giải thưởng và xác suất trúng giải.',
                'content' => $this->getArticleContent8(),
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(6),
                'view_count' => 2890,
            ],
            [
                'category_slug' => 'vietlott',
                'title' => 'Mega 6/45 vs Power 6/55 - Nên chơi loại nào?',
                'slug' => 'mega-645-vs-power-655-nen-choi-loai-nao',
                'excerpt' => 'So sánh chi tiết hai loại xổ số điện toán phổ biến nhất của Vietlott để giúp bạn lựa chọn.',
                'content' => $this->getArticleContent9(),
                'is_featured' => false,
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'view_count' => 1987,
            ],

            // Draft article
            [
                'category_slug' => 'tin-xo-so',
                'title' => 'Bài viết nháp - Chưa xuất bản',
                'slug' => 'bai-viet-nhap-chua-xuat-ban',
                'excerpt' => 'Đây là bài viết nháp để test.',
                'content' => '<p>Nội dung bài viết nháp.</p>',
                'is_featured' => false,
                'status' => 'draft',
                'published_at' => null,
                'view_count' => 0,
            ],
        ];

        $count = 0;
        foreach ($articles as $articleData) {
            $category = ArticleCategory::where('slug', $articleData['category_slug'])->first();

            if (!$category) {
                $this->command->warn("Category not found: {$articleData['category_slug']}");
                continue;
            }

            unset($articleData['category_slug']);

            Article::updateOrCreate(
                ['slug' => $articleData['slug']],
                array_merge($articleData, [
                    'category_id' => $category->id,
                    'author_id' => $authorId,
                ])
            );
            $count++;
        }

        $this->command->info("Created {$count} articles.");
    }

    private function getArticleContent1(): string
    {
        return <<<HTML
<h2>Kết quả xổ số miền Bắc hôm nay</h2>
<p>Xổ số miền Bắc (XSMB) là một trong những loại hình xổ số phổ biến nhất tại Việt Nam. Kết quả được công bố hàng ngày vào lúc 18h15.</p>

<h3>Cách xem kết quả XSMB</h3>
<p>Để xem kết quả XSMB nhanh và chính xác nhất, bạn có thể:</p>
<ul>
<li>Truy cập trang web chính thức của công ty xổ số</li>
<li>Theo dõi kết quả trực tiếp trên các kênh truyền hình</li>
<li>Sử dụng các website uy tín như XSKT.VN</li>
</ul>

<h3>Cơ cấu giải thưởng XSMB</h3>
<p>Xổ số miền Bắc có 9 hạng giải từ giải Đặc biệt đến giải 7, với tổng cộng 27 lần quay số mỗi ngày.</p>

<blockquote>
<p><strong>Lưu ý:</strong> Kết quả xổ số chỉ mang tính chất tham khảo. Vui lòng đối chiếu với vé số thực tế để xác nhận trúng thưởng.</p>
</blockquote>

<h3>Thống kê nhanh</h3>
<p>Dựa trên thống kê 30 ngày gần nhất, các con số xuất hiện nhiều nhất trong giải đặc biệt là: 23, 45, 67, 89, 01.</p>
HTML;
    }

    private function getArticleContent2(): string
    {
        return <<<HTML
<h2>Lịch quay xổ số miền Nam 2026</h2>
<p>Xổ số miền Nam có lịch quay đa dạng với nhiều đài xổ số khác nhau quay vào các ngày trong tuần.</p>

<h3>Lịch quay theo ngày</h3>
<p><strong>Thứ Hai:</strong> TP. Hồ Chí Minh, Đồng Tháp, Cà Mau</p>
<p><strong>Thứ Ba:</strong> Bến Tre, Vũng Tàu, Bạc Liêu</p>
<p><strong>Thứ Tư:</strong> Đồng Nai, Cần Thơ, Sóc Trăng</p>
<p><strong>Thứ Năm:</strong> Tây Ninh, An Giang, Bình Thuận</p>
<p><strong>Thứ Sáu:</strong> Vĩnh Long, Bình Dương, Trà Vinh</p>
<p><strong>Thứ Bảy:</strong> TP. Hồ Chí Minh, Long An, Bình Phước, Hậu Giang</p>
<p><strong>Chủ Nhật:</strong> Tiền Giang, Kiên Giang, Đà Lạt</p>

<h3>Thời gian quay số</h3>
<p>Xổ số miền Nam thường được quay vào lúc <strong>16h15</strong> hàng ngày. Kết quả sẽ được cập nhật ngay sau khi có thông tin chính thức.</p>
HTML;
    }

    private function getArticleContent3(): string
    {
        return <<<HTML
<h2>Các phương pháp soi cầu hiệu quả</h2>
<p>Soi cầu là phương pháp dựa vào thống kê và quy luật để dự đoán kết quả xổ số. Dưới đây là một số phương pháp phổ biến.</p>

<h3>1. Soi cầu theo bạch thủ</h3>
<p>Phương pháp này tập trung vào việc chọn một con số duy nhất dựa trên phân tích thống kê.</p>

<h3>2. Soi cầu theo lô gan</h3>
<p>Lô gan là những con số lâu không xuất hiện. Theo thống kê, các con số này có xác suất cao sẽ về trong những ngày tới.</p>

<h3>3. Soi cầu theo đầu đuôi</h3>
<p>Phân tích các con số theo đầu (chữ số hàng chục) và đuôi (chữ số hàng đơn vị) để tìm ra quy luật.</p>

<h3>4. Soi cầu theo tổng</h3>
<p>Tính tổng các chữ số của giải đặc biệt để dự đoán con số tiếp theo.</p>

<blockquote>
<p><strong>Lưu ý quan trọng:</strong> Xổ số là trò chơi may rủi. Các phương pháp soi cầu chỉ mang tính chất tham khảo và không đảm bảo kết quả.</p>
</blockquote>
HTML;
    }

    private function getArticleContent4(): string
    {
        return <<<HTML
<h2>Thống kê lô gan miền Bắc</h2>
<p>Lô gan là những con số lâu ngày chưa xuất hiện trong kết quả xổ số. Việc theo dõi lô gan giúp người chơi có thêm thông tin để tham khảo.</p>

<h3>Top lô gan hiện tại</h3>
<p>Dựa trên thống kê 30 ngày gần nhất, các con số gan nhất hiện tại bao gồm:</p>
<ul>
<li><strong>Số 23:</strong> Gan 15 ngày</li>
<li><strong>Số 67:</strong> Gan 12 ngày</li>
<li><strong>Số 89:</strong> Gan 10 ngày</li>
<li><strong>Số 45:</strong> Gan 9 ngày</li>
</ul>

<h3>Cách sử dụng thống kê lô gan</h3>
<p>Khi một con số gan quá lâu (thường trên 10-15 ngày), xác suất xuất hiện trở lại của nó tăng cao hơn theo quy luật thống kê.</p>

<p>Tuy nhiên, cần lưu ý rằng mỗi lần quay số là độc lập và xác suất xuất hiện của mọi con số là như nhau.</p>
HTML;
    }

    private function getArticleContent5(): string
    {
        return <<<HTML
<h2>Hướng dẫn chơi xổ số cho người mới</h2>
<p>Xổ số là hình thức giải trí hợp pháp tại Việt Nam. Bài viết này sẽ hướng dẫn bạn cách chơi xổ số cơ bản.</p>

<h3>1. Xổ số truyền thống</h3>
<p>Xổ số truyền thống bao gồm xổ số miền Bắc, miền Trung và miền Nam. Bạn mua vé với số đã in sẵn và chờ kết quả quay.</p>

<h4>Cách mua vé:</h4>
<ul>
<li>Mua tại các đại lý xổ số được cấp phép</li>
<li>Chọn vé có số bạn ưng ý</li>
<li>Giữ vé cẩn thận để đổi thưởng nếu trúng</li>
</ul>

<h3>2. Xổ số Vietlott</h3>
<p>Vietlott là xổ số điện toán tự chọn số. Bạn tự chọn các con số và mua vé tại các điểm bán hàng.</p>

<h4>Các loại hình Vietlott:</h4>
<ul>
<li><strong>Mega 6/45:</strong> Chọn 6 số từ 1-45</li>
<li><strong>Power 6/55:</strong> Chọn 6 số từ 1-55 + 1 số Power</li>
<li><strong>Max 3D:</strong> Chọn số có 3 chữ số</li>
</ul>

<h3>Lưu ý quan trọng</h3>
<blockquote>
<p>Chơi xổ số có trách nhiệm. Chỉ dành một khoản tiền nhỏ để giải trí và không nên coi đây là nguồn thu nhập.</p>
</blockquote>
HTML;
    }

    private function getArticleContent6(): string
    {
        return <<<HTML
<h2>So sánh cơ cấu giải thưởng xổ số các miền</h2>
<p>Mỗi miền có cơ cấu giải thưởng khác nhau. Cùng tìm hiểu chi tiết để lựa chọn phù hợp.</p>

<h3>Xổ số miền Bắc</h3>
<ul>
<li>Giải đặc biệt: 200.000.000đ</li>
<li>Giải nhất: 50.000.000đ</li>
<li>Giải nhì: 15.000.000đ</li>
<li>Giải ba: 5.000.000đ</li>
<li>Giá vé: 10.000đ</li>
</ul>

<h3>Xổ số miền Trung & miền Nam</h3>
<ul>
<li>Giải đặc biệt: 2.000.000.000đ</li>
<li>Giải nhất: 30.000.000đ</li>
<li>Giải nhì: 15.000.000đ</li>
<li>Giá vé: 10.000đ</li>
</ul>

<h3>Nhận xét</h3>
<p>Xổ số miền Nam có giải đặc biệt cao hơn nhiều (2 tỷ so với 200 triệu), nhưng xác suất trúng cũng thấp hơn do có nhiều chữ số hơn.</p>
HTML;
    }

    private function getArticleContent7(): string
    {
        return <<<HTML
<h2>Câu chuyện người trúng Jackpot 150 tỷ</h2>
<p>Đây là câu chuyện có thật về một người may mắn trúng giải Jackpot Vietlott và cuộc sống sau đó của họ.</p>

<h3>Khoảnh khắc may mắn</h3>
<p>Anh T. (giấu tên) là một nhân viên văn phòng bình thường tại TP.HCM. Anh có thói quen mua vé số Vietlott mỗi tuần với số tiền khoảng 100.000đ.</p>

<p>"Tôi không tin vào mắt mình khi kiểm tra kết quả. Tôi phải kiểm tra đi kiểm tra lại nhiều lần," anh T. chia sẻ.</p>

<h3>Cuộc sống sau khi trúng giải</h3>
<p>Sau khi nhận giải, anh T. đã:</p>
<ul>
<li>Trả hết các khoản nợ cho gia đình</li>
<li>Mua nhà mới cho bố mẹ</li>
<li>Đầu tư vào bất động sản và chứng khoán</li>
<li>Tiếp tục làm việc bình thường</li>
</ul>

<blockquote>
<p>"Tiền bạc không thay đổi con người tôi. Tôi vẫn sống giản dị và làm việc chăm chỉ như trước," anh T. chia sẻ.</p>
</blockquote>

<h3>Lời khuyên từ người trong cuộc</h3>
<p>Anh T. khuyên mọi người nên chơi xổ số có trách nhiệm và không nên đặt quá nhiều kỳ vọng vào việc trúng giải.</p>
HTML;
    }

    private function getArticleContent8(): string
    {
        return <<<HTML
<h2>Hướng dẫn chơi Power 6/55</h2>
<p>Power 6/55 là một trong những loại xổ số điện toán hấp dẫn nhất của Vietlott với giải Jackpot có thể lên đến hàng trăm tỷ đồng.</p>

<h3>Cách chơi</h3>
<ol>
<li>Chọn 6 con số từ 1 đến 55</li>
<li>Hệ thống sẽ tự động chọn 1 số Power từ 1 đến 55</li>
<li>Mỗi vé có giá 10.000đ</li>
</ol>

<h3>Cơ cấu giải thưởng</h3>
<ul>
<li><strong>Jackpot 1:</strong> Trùng 6 số + Power (tỷ lệ 1:139.838.160)</li>
<li><strong>Jackpot 2:</strong> Trùng 6 số (tỷ lệ 1:2.587.744)</li>
<li><strong>Giải 3:</strong> Trùng 5 số + Power</li>
<li><strong>Giải 4:</strong> Trùng 5 số</li>
<li><strong>Giải 5:</strong> Trùng 4 số + Power</li>
</ul>

<h3>Mẹo chơi Power 6/55</h3>
<p>Một số người chơi có kinh nghiệm thường:</p>
<ul>
<li>Kết hợp số chẵn và số lẻ</li>
<li>Không chọn các số liên tiếp</li>
<li>Tham khảo thống kê các số hay về</li>
</ul>
HTML;
    }

    private function getArticleContent9(): string
    {
        return <<<HTML
<h2>Mega 6/45 vs Power 6/55 - Nên chọn loại nào?</h2>
<p>Cả hai loại xổ số đều có những ưu và nhược điểm riêng. Cùng so sánh để đưa ra lựa chọn phù hợp.</p>

<h3>Mega 6/45</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
<li>Xác suất trúng Jackpot cao hơn (1:8.145.060)</li>
<li>Dễ chơi, chỉ cần chọn 6 số từ 1-45</li>
<li>Quay 3 lần/tuần (Thứ 4, 6, CN)</li>
</ul>
<p><strong>Nhược điểm:</strong></p>
<ul>
<li>Giải Jackpot thường thấp hơn Power</li>
</ul>

<h3>Power 6/55</h3>
<p><strong>Ưu điểm:</strong></p>
<ul>
<li>Giải Jackpot thường rất cao (có thể lên 300 tỷ+)</li>
<li>Có 2 giải Jackpot (Jackpot 1 và Jackpot 2)</li>
<li>Quay 3 lần/tuần (Thứ 3, 5, 7)</li>
</ul>
<p><strong>Nhược điểm:</strong></p>
<ul>
<li>Xác suất trúng thấp hơn (1:139.838.160 cho Jackpot 1)</li>
</ul>

<h3>Kết luận</h3>
<p>Nếu bạn muốn có cơ hội trúng giải cao hơn, hãy chọn <strong>Mega 6/45</strong>. Nếu bạn muốn "săn" giải thưởng lớn, <strong>Power 6/55</strong> là lựa chọn phù hợp.</p>
HTML;
    }
}
