<?php

namespace Database\Seeders;

use App\Models\FooterColumn;
use App\Models\FooterLink;
use Illuminate\Database\Seeder;

class FooterSeeder extends Seeder
{
    public function run(): void
    {
        // Column 1: About (site name + about text)
        $aboutColumn = FooterColumn::updateOrCreate(
            ['type' => 'about'],
            [
                'title' => '{site_name}',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        // Column 2: Quick Links
        $linksColumn = FooterColumn::updateOrCreate(
            ['title' => 'Liên kết nhanh', 'type' => 'links'],
            [
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Column 3: Info (copyright + disclaimer)
        $infoColumn = FooterColumn::updateOrCreate(
            ['type' => 'info'],
            [
                'title' => 'Thông tin',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // Seed links for "Liên kết nhanh" column
        $links = [
            ['label' => 'XSMB', 'type' => 'route', 'route_name' => 'xsmb', 'sort_order' => 1],
            ['label' => 'XSMT', 'type' => 'route', 'route_name' => 'xsmt', 'sort_order' => 2],
            ['label' => 'XSMN', 'type' => 'route', 'route_name' => 'xsmn', 'sort_order' => 3],
            ['label' => 'Sổ kết quả', 'type' => 'route', 'route_name' => 'results.book', 'sort_order' => 4],
            ['label' => 'Thống kê', 'type' => 'route', 'route_name' => 'statistics', 'sort_order' => 5],
        ];

        foreach ($links as $linkData) {
            FooterLink::updateOrCreate(
                [
                    'footer_column_id' => $linksColumn->id,
                    'label' => $linkData['label'],
                ],
                [
                    'type' => $linkData['type'],
                    'route_name' => $linkData['route_name'],
                    'url' => null,
                    'open_in_new_tab' => false,
                    'sort_order' => $linkData['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Footer columns and links seeded successfully!');
    }
}
