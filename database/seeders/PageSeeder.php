<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Điều khoản và điều kiện sử dụng',
                'slug' => 'dieu-khoan-su-dung',
                'content' => $this->getTermsContent(),
                'status' => 'published',
                'published_at' => now(),
                'view_count' => 0,
                'sort_order' => 1,
                'meta_title' => 'Điều khoản và điều kiện sử dụng',
                'meta_description' => 'Điều khoản và điều kiện sử dụng website tra cứu kết quả xổ số kiến thiết 3 miền.',
            ],
            [
                'title' => 'Liên hệ với chúng tôi',
                'slug' => 'lien-he',
                'content' => $this->getContactContent(),
                'status' => 'published',
                'published_at' => now(),
                'view_count' => 0,
                'sort_order' => 2,
                'meta_title' => 'Liên hệ với chúng tôi',
                'meta_description' => 'Liên hệ với chúng tôi để được hỗ trợ và giải đáp thắc mắc.',
            ],
            [
                'title' => 'Giới thiệu',
                'slug' => 'gioi-thieu',
                'content' => $this->getAboutContent(),
                'status' => 'published',
                'published_at' => now(),
                'view_count' => 0,
                'sort_order' => 3,
                'meta_title' => 'Giới thiệu về XSKT.VN',
                'meta_description' => 'Giới thiệu về XSKT.VN - Trang web tra cứu kết quả xổ số kiến thiết 3 miền nhanh chóng, chính xác và miễn phí.',
            ],
        ];

        $count = 0;
        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
            $count++;
        }

        $this->command->info("Created {$count} pages.");
    }

    private function getTermsContent(): string
    {
        return <<<'HTML'
<h2>1. Giới thiệu</h2>
<p>Chào mừng bạn đến với <strong>XSKT.VN</strong>. Khi truy cập và sử dụng website này, bạn đồng ý tuân thủ các điều khoản và điều kiện được nêu dưới đây. Vui lòng đọc kỹ trước khi sử dụng dịch vụ của chúng tôi.</p>

<h2>2. Mục đích sử dụng</h2>
<p>Website XSKT.VN được xây dựng với mục đích:</p>
<ul>
<li>Cung cấp kết quả xổ số kiến thiết 3 miền (Bắc, Trung, Nam) nhanh chóng và chính xác</li>
<li>Cung cấp các công cụ thống kê, phân tích kết quả xổ số</li>
<li>Cung cấp thông tin, tin tức liên quan đến ngành xổ số</li>
</ul>
<p>Website chỉ mang tính chất <strong>tra cứu và tham khảo</strong>, không phải là tổ chức phát hành hoặc kinh doanh xổ số.</p>

<h2>3. Quyền sở hữu trí tuệ</h2>
<p>Toàn bộ nội dung trên website bao gồm nhưng không giới hạn: văn bản, hình ảnh, thiết kế giao diện, mã nguồn, logo và các tài liệu khác đều thuộc quyền sở hữu của XSKT.VN hoặc các bên cấp phép liên quan.</p>
<p>Bạn <strong>không được phép</strong> sao chép, phân phối, sửa đổi hoặc sử dụng bất kỳ nội dung nào trên website cho mục đích thương mại mà không có sự đồng ý bằng văn bản của chúng tôi.</p>

<h2>4. Trách nhiệm của người dùng</h2>
<p>Khi sử dụng website, bạn cam kết:</p>
<ul>
<li>Không sử dụng website cho bất kỳ mục đích bất hợp pháp nào</li>
<li>Không can thiệp, phá hoại hoặc làm gián đoạn hoạt động của website</li>
<li>Không thu thập thông tin của người dùng khác mà không được phép</li>
<li>Không đăng tải nội dung vi phạm pháp luật, đạo đức hoặc thuần phong mỹ tục</li>
<li>Tuân thủ mọi quy định pháp luật hiện hành của Việt Nam</li>
</ul>

<h2>5. Giới hạn trách nhiệm</h2>
<p>XSKT.VN nỗ lực cung cấp thông tin chính xác và kịp thời. Tuy nhiên, chúng tôi <strong>không đảm bảo</strong>:</p>
<ul>
<li>Tính chính xác tuyệt đối của mọi thông tin trên website</li>
<li>Website hoạt động liên tục không bị gián đoạn</li>
<li>Website không có lỗi kỹ thuật hoặc virus</li>
</ul>
<p>Kết quả xổ số trên website chỉ mang tính chất tham khảo. Người dùng cần đối chiếu với vé số thực tế và thông tin chính thức từ các công ty xổ số để xác nhận trúng thưởng.</p>

<h2>6. Quyền riêng tư</h2>
<p>Chúng tôi tôn trọng quyền riêng tư của người dùng. Thông tin cá nhân của bạn (nếu có) sẽ được bảo mật và chỉ sử dụng cho mục đích cải thiện dịch vụ. Chúng tôi không bán hoặc chia sẻ thông tin cá nhân của bạn cho bên thứ ba.</p>

<h2>7. Cookie và công nghệ theo dõi</h2>
<p>Website có thể sử dụng cookie và các công nghệ tương tự để:</p>
<ul>
<li>Cải thiện trải nghiệm người dùng</li>
<li>Phân tích lưu lượng truy cập</li>
<li>Ghi nhớ tùy chọn của bạn</li>
</ul>
<p>Bạn có thể tắt cookie trong trình duyệt, tuy nhiên một số tính năng của website có thể không hoạt động đầy đủ.</p>

<h2>8. Liên kết đến website bên ngoài</h2>
<p>Website có thể chứa các liên kết đến website của bên thứ ba. Chúng tôi <strong>không chịu trách nhiệm</strong> về nội dung, chính sách bảo mật hoặc hoạt động của các website bên ngoài này.</p>

<h2>9. Thay đổi điều khoản</h2>
<p>Chúng tôi có quyền thay đổi, bổ sung hoặc cập nhật các điều khoản này vào bất kỳ lúc nào mà không cần thông báo trước. Việc bạn tiếp tục sử dụng website sau khi các thay đổi được đăng tải đồng nghĩa với việc bạn chấp nhận các điều khoản mới.</p>

<h2>10. Luật áp dụng</h2>
<p>Các điều khoản này được điều chỉnh và giải thích theo pháp luật Việt Nam. Mọi tranh chấp phát sinh sẽ được giải quyết tại cơ quan có thẩm quyền của Việt Nam.</p>

<h2>11. Liên hệ</h2>
<p>Nếu bạn có bất kỳ câu hỏi nào về các điều khoản và điều kiện này, vui lòng <a href="/lien-he">liên hệ với chúng tôi</a>.</p>
HTML;
    }

    private function getContactContent(): string
    {
        return <<<'HTML'
<p>Cảm ơn bạn đã quan tâm đến <strong>XSKT.VN</strong>. Nếu bạn có bất kỳ câu hỏi, góp ý hoặc yêu cầu hỗ trợ nào, đừng ngần ngại liên hệ với chúng tôi. Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn.</p>

<h2>Thông tin liên hệ</h2>
<p><strong>Email:</strong> {contact_email}</p>
<p>Chúng tôi sẽ phản hồi trong vòng 24 giờ làm việc.</p>
HTML;
    }

    private function getAboutContent(): string
    {
        return <<<'HTML'
<h2>Về chúng tôi</h2>
<p><strong>XSKT.VN</strong> là trang web tra cứu kết quả xổ số kiến thiết 3 miền (Bắc, Trung, Nam) nhanh chóng, chính xác và hoàn toàn miễn phí. Chúng tôi cam kết mang đến cho người dùng trải nghiệm tốt nhất khi tra cứu thông tin xổ số.</p>

<h2>Sứ mệnh của chúng tôi</h2>
<ul>
<li><strong>Nhanh chóng:</strong> Cập nhật kết quả xổ số ngay khi có thông tin chính thức từ các đài xổ số</li>
<li><strong>Chính xác:</strong> Đảm bảo tính chính xác tuyệt đối của mọi kết quả được đăng tải</li>
<li><strong>Miễn phí:</strong> Tất cả dịch vụ tra cứu đều hoàn toàn miễn phí cho người dùng</li>
<li><strong>Tiện lợi:</strong> Giao diện thân thiện, dễ sử dụng trên mọi thiết bị</li>
</ul>

<h2>Dịch vụ của chúng tôi</h2>
<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin: 20px 0;">
<div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
<h3 style="margin-top: 0; color: #1a1a1a;">Kết quả xổ số</h3>
<p style="color: #4b5563; margin-bottom: 0;">Tra cứu kết quả xổ số kiến thiết 3 miền Bắc, Trung, Nam hàng ngày với tốc độ cập nhật nhanh nhất.</p>
</div>
<div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
<h3 style="margin-top: 0; color: #1a1a1a;">Thống kê xổ số</h3>
<p style="color: #4b5563; margin-bottom: 0;">Các công cụ thống kê chi tiết giúp bạn phân tích xu hướng và tần suất xuất hiện của các con số.</p>
</div>
<div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
<h3 style="margin-top: 0; color: #1a1a1a;">Soi cầu & Dự đoán</h3>
<p style="color: #4b5563; margin-bottom: 0;">Tham khảo các phương pháp soi cầu dựa trên phân tích thống kê và dữ liệu lịch sử.</p>
</div>
<div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px;">
<h3 style="margin-top: 0; color: #1a1a1a;">Tin tức xổ số</h3>
<p style="color: #4b5563; margin-bottom: 0;">Cập nhật tin tức mới nhất về ngành xổ số, các câu chuyện trúng giải và kiến thức hữu ích.</p>
</div>
</div>

<h2>Cam kết của chúng tôi</h2>
<div style="background-color: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; padding: 20px; margin: 20px 0;">
<ul style="margin: 0; padding-left: 20px;">
<li style="margin-bottom: 8px;">Cung cấp kết quả xổ số <strong>chính xác 100%</strong> từ nguồn chính thức</li>
<li style="margin-bottom: 8px;">Cập nhật kết quả <strong>nhanh nhất</strong> ngay khi có thông tin</li>
<li style="margin-bottom: 8px;">Giao diện <strong>thân thiện</strong>, tối ưu cho mọi thiết bị</li>
<li style="margin-bottom: 8px;">Bảo mật thông tin và <strong>tôn trọng quyền riêng tư</strong> của người dùng</li>
<li style="margin-bottom: 0;">Dịch vụ hoàn toàn <strong>miễn phí</strong>, không thu bất kỳ phí nào</li>
</ul>
</div>

<h2>Liên hệ với chúng tôi</h2>
<p>Nếu bạn có bất kỳ câu hỏi, góp ý hoặc yêu cầu hợp tác nào, vui lòng truy cập trang <a href="/lien-he">Liên hệ</a> để gửi thông tin cho chúng tôi. Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn.</p>
HTML;
    }
}
