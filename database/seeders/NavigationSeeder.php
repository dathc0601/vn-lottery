<?php

namespace Database\Seeders;

use App\Models\NavigationItem;
use Illuminate\Database\Seeder;

class NavigationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing navigation items
        NavigationItem::truncate();

        $items = [
            [
                'title' => 'Trang chủ',
                'type' => 'route',
                'route_name' => 'home',
                'active_pattern' => 'home',
                'sort_order' => 1,
            ],
            [
                'title' => 'XSMB',
                'type' => 'xsmb_days',
                'active_pattern' => 'xsmb*',
                'dropdown_type' => 'simple',
                'sort_order' => 2,
            ],
            [
                'title' => 'XSMT',
                'type' => 'xsmt_days',
                'active_pattern' => 'xsmt*',
                'dropdown_type' => 'simple',
                'sort_order' => 3,
            ],
            [
                'title' => 'XSMN',
                'type' => 'xsmn_days',
                'active_pattern' => 'xsmn*',
                'dropdown_type' => 'simple',
                'sort_order' => 4,
            ],
            [
                'title' => 'Sổ kết quả',
                'type' => 'route',
                'route_name' => 'results.book',
                'active_pattern' => 'results.book',
                'sort_order' => 5,
            ],
            [
                'title' => 'Thống kê',
                'type' => 'route',
                'route_name' => 'statistics',
                'active_pattern' => 'statistics',
                'sort_order' => 6,
            ],
            [
                'title' => 'Dò vé số',
                'type' => 'route',
                'route_name' => 'ticket.verify',
                'active_pattern' => 'ticket.verify',
                'sort_order' => 7,
            ],
            [
                'title' => 'Lịch mở thưởng',
                'type' => 'route',
                'route_name' => 'schedule',
                'active_pattern' => 'schedule',
                'sort_order' => 8,
            ],
            [
                'title' => 'Quay thử',
                'type' => 'route',
                'route_name' => 'trial.draw',
                'active_pattern' => 'trial.draw',
                'sort_order' => 9,
            ],
            [
                'title' => 'Vietlott',
                'type' => 'route',
                'route_name' => 'vietlott',
                'active_pattern' => 'vietlott',
                'sort_order' => 10,
            ],
        ];

        foreach ($items as $item) {
            NavigationItem::create(array_merge([
                'is_active' => true,
                'open_in_new_tab' => false,
                'dropdown_type' => 'simple',
            ], $item));
        }

        $this->command->info('Navigation items seeded successfully!');
    }
}
