<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinces = [
            // North Region (XSMB) - 6 provinces
            [
                'name' => 'Hà Nội',
                'code' => 'hn',
                'region' => 'north',
                'slug' => 'ha-noi',
                'draw_days' => [1, 2, 3, 4, 5, 6, 7], // Daily
                'draw_time' => '18:15:00',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Quảng Ninh',
                'code' => 'qni',
                'region' => 'north',
                'slug' => 'quang-ninh',
                'draw_days' => [2], // Tuesday
                'draw_time' => '18:15:00',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Bắc Ninh',
                'code' => 'bni',
                'region' => 'north',
                'slug' => 'bac-ninh',
                'draw_days' => [3], // Wednesday
                'draw_time' => '18:15:00',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Hải Phòng',
                'code' => 'hp',
                'region' => 'north',
                'slug' => 'hai-phong',
                'draw_days' => [5], // Friday
                'draw_time' => '18:15:00',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Nam Định',
                'code' => 'nd',
                'region' => 'north',
                'slug' => 'nam-dinh',
                'draw_days' => [6], // Saturday
                'draw_time' => '18:15:00',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Thái Bình',
                'code' => 'tb',
                'region' => 'north',
                'slug' => 'thai-binh',
                'draw_days' => [7], // Sunday
                'draw_time' => '18:15:00',
                'sort_order' => 7,
                'is_active' => true,
            ],

            // Central Region (XSMT) - 14 provinces
            [
                'name' => 'Quảng Ngãi',
                'code' => 'qng',
                'region' => 'central',
                'slug' => 'quang-ngai',
                'draw_days' => [6], // Saturday
                'draw_time' => '17:15:00',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Đà Nẵng',
                'code' => 'dna',
                'region' => 'central',
                'slug' => 'da-nang',
                'draw_days' => [3, 6], // Wednesday, Saturday
                'draw_time' => '17:15:00',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Đắk Nông',
                'code' => 'dno',
                'region' => 'central',
                'slug' => 'dak-nong',
                'draw_days' => [6], // Saturday
                'draw_time' => '17:15:00',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Ninh Thuận',
                'code' => 'nt',
                'region' => 'central',
                'slug' => 'ninh-thuan',
                'draw_days' => [5], // Friday
                'draw_time' => '17:15:00',
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'Gia Lai',
                'code' => 'gl',
                'region' => 'central',
                'slug' => 'gia-lai',
                'draw_days' => [5], // Friday
                'draw_time' => '17:15:00',
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Quảng Trị',
                'code' => 'qt',
                'region' => 'central',
                'slug' => 'quang-tri',
                'draw_days' => [4], // Thursday
                'draw_time' => '17:15:00',
                'sort_order' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Quảng Bình',
                'code' => 'qb',
                'region' => 'central',
                'slug' => 'quang-binh',
                'draw_days' => [4], // Thursday
                'draw_time' => '17:15:00',
                'sort_order' => 16,
                'is_active' => true,
            ],
            [
                'name' => 'Bình Định',
                'code' => 'bdi',
                'region' => 'central',
                'slug' => 'binh-dinh',
                'draw_days' => [4], // Thursday
                'draw_time' => '17:15:00',
                'sort_order' => 17,
                'is_active' => true,
            ],
            [
                'name' => 'Khánh Hoà',
                'code' => 'kh',
                'region' => 'central',
                'slug' => 'khanh-hoa',
                'draw_days' => [3, 7], // Wednesday, Sunday
                'draw_time' => '17:15:00',
                'sort_order' => 18,
                'is_active' => true,
            ],
            [
                'name' => 'Quảng Nam',
                'code' => 'qna',
                'region' => 'central',
                'slug' => 'quang-nam',
                'draw_days' => [2], // Tuesday
                'draw_time' => '17:15:00',
                'sort_order' => 19,
                'is_active' => true,
            ],
            [
                'name' => 'Đắk Lắk',
                'code' => 'dl',
                'region' => 'central',
                'slug' => 'dak-lak',
                'draw_days' => [2], // Tuesday
                'draw_time' => '17:15:00',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Thừa Thiên Huế',
                'code' => 'th',
                'region' => 'central',
                'slug' => 'thua-thien-hue',
                'draw_days' => [1, 7], // Monday, Sunday
                'draw_time' => '17:15:00',
                'sort_order' => 21,
                'is_active' => true,
            ],
            [
                'name' => 'Phú Yên',
                'code' => 'py',
                'region' => 'central',
                'slug' => 'phu-yen',
                'draw_days' => [1], // Monday
                'draw_time' => '17:15:00',
                'sort_order' => 22,
                'is_active' => true,
            ],
            [
                'name' => 'Kon Tum',
                'code' => 'kt',
                'region' => 'central',
                'slug' => 'kon-tum',
                'draw_days' => [7], // Sunday
                'draw_time' => '17:15:00',
                'sort_order' => 23,
                'is_active' => true,
            ],

            // South Region (XSMN) - 21 provinces
            [
                'name' => 'Hậu Giang',
                'code' => 'hg',
                'region' => 'south',
                'slug' => 'hau-giang',
                'draw_days' => [6], // Saturday
                'draw_time' => '16:15:00',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Hồ Chí Minh',
                'code' => 'tphc',
                'region' => 'south',
                'slug' => 'ho-chi-minh',
                'draw_days' => [1, 6], // Monday, Saturday
                'draw_time' => '16:15:00',
                'sort_order' => 31,
                'is_active' => true,
            ],
            [
                'name' => 'Bình Phước',
                'code' => 'bp',
                'region' => 'south',
                'slug' => 'binh-phuoc',
                'draw_days' => [6], // Saturday
                'draw_time' => '16:15:00',
                'sort_order' => 32,
                'is_active' => true,
            ],
            [
                'name' => 'Long An',
                'code' => 'la',
                'region' => 'south',
                'slug' => 'long-an',
                'draw_days' => [6], // Saturday
                'draw_time' => '16:15:00',
                'sort_order' => 33,
                'is_active' => true,
            ],
            [
                'name' => 'Bình Dương',
                'code' => 'bdu',
                'region' => 'south',
                'slug' => 'binh-duong',
                'draw_days' => [5], // Friday
                'draw_time' => '16:15:00',
                'sort_order' => 34,
                'is_active' => true,
            ],
            [
                'name' => 'Trà Vinh',
                'code' => 'tv',
                'region' => 'south',
                'slug' => 'tra-vinh',
                'draw_days' => [5], // Friday
                'draw_time' => '16:15:00',
                'sort_order' => 35,
                'is_active' => true,
            ],
            [
                'name' => 'Vĩnh Long',
                'code' => 'vl',
                'region' => 'south',
                'slug' => 'vinh-long',
                'draw_days' => [5], // Friday
                'draw_time' => '16:15:00',
                'sort_order' => 36,
                'is_active' => true,
            ],
            [
                'name' => 'Bình Thuận',
                'code' => 'bith',
                'region' => 'south',
                'slug' => 'binh-thuan',
                'draw_days' => [4], // Thursday
                'draw_time' => '16:15:00',
                'sort_order' => 37,
                'is_active' => true,
            ],
            [
                'name' => 'Tây Ninh',
                'code' => 'tn',
                'region' => 'south',
                'slug' => 'tay-ninh',
                'draw_days' => [4], // Thursday
                'draw_time' => '16:15:00',
                'sort_order' => 38,
                'is_active' => true,
            ],
            [
                'name' => 'An Giang',
                'code' => 'ag',
                'region' => 'south',
                'slug' => 'an-giang',
                'draw_days' => [4], // Thursday
                'draw_time' => '16:15:00',
                'sort_order' => 39,
                'is_active' => true,
            ],
            [
                'name' => 'Sóc Trăng',
                'code' => 'st',
                'region' => 'south',
                'slug' => 'soc-trang',
                'draw_days' => [3], // Wednesday
                'draw_time' => '16:15:00',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Đồng Nai',
                'code' => 'dni',
                'region' => 'south',
                'slug' => 'dong-nai',
                'draw_days' => [3], // Wednesday
                'draw_time' => '16:15:00',
                'sort_order' => 41,
                'is_active' => true,
            ],
            [
                'name' => 'Cần Thơ',
                'code' => 'ct',
                'region' => 'south',
                'slug' => 'can-tho',
                'draw_days' => [3], // Wednesday
                'draw_time' => '16:15:00',
                'sort_order' => 42,
                'is_active' => true,
            ],
            [
                'name' => 'Vũng Tàu',
                'code' => 'vt',
                'region' => 'south',
                'slug' => 'vung-tau',
                'draw_days' => [2], // Tuesday
                'draw_time' => '16:15:00',
                'sort_order' => 43,
                'is_active' => true,
            ],
            [
                'name' => 'Bạc Liêu',
                'code' => 'bl',
                'region' => 'south',
                'slug' => 'bac-lieu',
                'draw_days' => [2], // Tuesday
                'draw_time' => '16:15:00',
                'sort_order' => 44,
                'is_active' => true,
            ],
            [
                'name' => 'Bến Tre',
                'code' => 'bt',
                'region' => 'south',
                'slug' => 'ben-tre',
                'draw_days' => [2], // Tuesday
                'draw_time' => '16:15:00',
                'sort_order' => 44.5,
                'is_active' => true,
            ],
            [
                'name' => 'Cà Mau',
                'code' => 'cm',
                'region' => 'south',
                'slug' => 'ca-mau',
                'draw_days' => [1], // Monday
                'draw_time' => '16:15:00',
                'sort_order' => 45,
                'is_active' => true,
            ],
            [
                'name' => 'Đồng Tháp',
                'code' => 'dt',
                'region' => 'south',
                'slug' => 'dong-thap',
                'draw_days' => [1], // Monday
                'draw_time' => '16:15:00',
                'sort_order' => 46,
                'is_active' => true,
            ],
            [
                'name' => 'Đà Lạt',
                'code' => 'dalat',
                'region' => 'south',
                'slug' => 'da-lat',
                'draw_days' => [7], // Sunday
                'draw_time' => '16:15:00',
                'sort_order' => 47,
                'is_active' => true,
            ],
            [
                'name' => 'Tiền Giang',
                'code' => 'tg',
                'region' => 'south',
                'slug' => 'tien-giang',
                'draw_days' => [7], // Sunday
                'draw_time' => '16:15:00',
                'sort_order' => 48,
                'is_active' => true,
            ],
            [
                'name' => 'Kiên Giang',
                'code' => 'kg',
                'region' => 'south',
                'slug' => 'kien-giang',
                'draw_days' => [7], // Sunday
                'draw_time' => '16:15:00',
                'sort_order' => 49,
                'is_active' => true,
            ],
        ];

        foreach ($provinces as $province) {
            Province::updateOrCreate(
                ['slug' => $province['slug']], // Find by slug
                $province // Update or create with these values
            );
        }
    }
}
