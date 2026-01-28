<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class FooterExtendedSeeder extends Seeder
{
    public function run(): void
    {
        $infoTableRows = [
            ['label' => 'Tên website', 'value' => 'XSKT.VN - Xổ số kiến thiết Việt Nam'],
            ['label' => 'Địa chỉ', 'value' => 'Việt Nam'],
            ['label' => 'Nội dung', 'value' => 'Kết quả xổ số 3 miền trực tiếp hàng ngày, thống kê, soi cầu, dò vé số'],
            ['label' => 'Giờ mở thưởng XSMB', 'value' => '18:15 hàng ngày'],
            ['label' => 'Giờ mở thưởng XSMT', 'value' => '17:15 hàng ngày'],
            ['label' => 'Giờ mở thưởng XSMN', 'value' => '16:15 hàng ngày'],
            ['label' => 'Nguồn dữ liệu', 'value' => 'Công ty TNHH MTV Xổ số kiến thiết các tỉnh'],
        ];

        $notes = [
            ['text' => 'Kết quả xổ số trên XSKT.VN chỉ mang tính chất tham khảo, không phải kết quả chính thức từ Hội đồng xổ số.'],
            ['text' => 'Người chơi vui lòng đối chiếu kết quả với đài phát thanh hoặc trung tâm xổ số kiến thiết các tỉnh để xác nhận chính xác.'],
            ['text' => 'XSKT.VN không chịu trách nhiệm về bất kỳ sai sót nào trong quá trình cập nhật dữ liệu.'],
            ['text' => 'Nghiêm cấm sử dụng thông tin trên website cho mục đích cá cược bất hợp pháp.'],
        ];

        $referenceLinks = [
            ['label' => 'Xổ số Miền Bắc', 'url' => '/xsmb', 'new_tab' => false],
            ['label' => 'Xổ số Miền Trung', 'url' => '/xsmt', 'new_tab' => false],
            ['label' => 'Xổ số Miền Nam', 'url' => '/xsmn', 'new_tab' => false],
            ['label' => 'Vietlott', 'url' => '/vietlott', 'new_tab' => false],
            ['label' => 'Thống kê', 'url' => '/thong-ke', 'new_tab' => false],
            ['label' => 'Soi cầu', 'url' => '/soi-cau', 'new_tab' => false],
        ];

        $settings = [
            [
                'group' => 'footer',
                'key' => 'intro_title',
                'value' => 'XSKT.VN - Kết quả xổ số 3 miền nhanh nhất',
                'type' => 'text',
            ],
            [
                'group' => 'footer',
                'key' => 'intro_text',
                'value' => 'XSKT.VN là trang web cung cấp kết quả xổ số kiến thiết 3 miền Bắc, Trung, Nam nhanh và chính xác nhất. Chúng tôi cập nhật trực tiếp kết quả xổ số hàng ngày từ các đài xổ số trên toàn quốc, bao gồm XSMB, XSMT, XSMN và Vietlott. Ngoài ra, XSKT.VN còn cung cấp các công cụ thống kê, soi cầu, dò vé số giúp người chơi tiện lợi hơn trong việc theo dõi và phân tích kết quả xổ số.',
                'type' => 'textarea',
            ],
            [
                'group' => 'footer',
                'key' => 'info_table_rows',
                'value' => json_encode($infoTableRows, JSON_UNESCAPED_UNICODE),
                'type' => 'json',
            ],
            [
                'group' => 'footer',
                'key' => 'notes',
                'value' => json_encode($notes, JSON_UNESCAPED_UNICODE),
                'type' => 'json',
            ],
            [
                'group' => 'footer',
                'key' => 'reference_links',
                'value' => json_encode($referenceLinks, JSON_UNESCAPED_UNICODE),
                'type' => 'json',
            ],
            [
                'group' => 'footer',
                'key' => 'show_schedule',
                'value' => '1',
                'type' => 'toggle',
            ],
            [
                'group' => 'footer',
                'key' => 'show_bottom_nav',
                'value' => '1',
                'type' => 'toggle',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        $this->command->info('Extended footer settings seeded successfully!');
    }
}
