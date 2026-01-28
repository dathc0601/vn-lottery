<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class ArticleCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Tin xổ số',
                'slug' => 'tin-xo-so',
                'description' => 'Cập nhật tin tức mới nhất về xổ số các miền',
                'meta_title' => 'Tin xổ số - Cập nhật kết quả xổ số mới nhất',
                'meta_description' => 'Tin tức xổ số mới nhất, kết quả xổ số các miền Bắc, Trung, Nam và Vietlott.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Soi cầu - Dự đoán',
                'slug' => 'soi-cau-du-doan',
                'description' => 'Phân tích, soi cầu và dự đoán kết quả xổ số',
                'meta_title' => 'Soi cầu xổ số - Dự đoán kết quả chính xác',
                'meta_description' => 'Soi cầu xổ số, phân tích thống kê và dự đoán kết quả xổ số miền Bắc, miền Trung, miền Nam.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Kiến thức xổ số',
                'slug' => 'kien-thuc-xo-so',
                'description' => 'Chia sẻ kiến thức, kinh nghiệm chơi xổ số',
                'meta_title' => 'Kiến thức xổ số - Hướng dẫn và kinh nghiệm',
                'meta_description' => 'Kiến thức về xổ số, hướng dẫn cách chơi, kinh nghiệm và mẹo hay cho người chơi xổ số.',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Người trúng giải',
                'slug' => 'nguoi-trung-giai',
                'description' => 'Câu chuyện về những người trúng giải xổ số',
                'meta_title' => 'Người trúng giải - Câu chuyện may mắn',
                'meta_description' => 'Những câu chuyện có thật về người trúng giải xổ số, jackpot Vietlott và cách họ đổi đời.',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Vietlott',
                'slug' => 'vietlott',
                'description' => 'Tin tức và thông tin về xổ số Vietlott',
                'meta_title' => 'Vietlott - Tin tức xổ số điện toán',
                'meta_description' => 'Cập nhật tin tức Vietlott, kết quả Mega 6/45, Power 6/55, Max 3D và các chương trình khuyến mãi.',
                'sort_order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            ArticleCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        $this->command->info('Created ' . count($categories) . ' article categories.');
    }
}
