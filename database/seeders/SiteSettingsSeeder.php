<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Tab 1: General
            ['group' => 'general', 'key' => 'site_name', 'value' => 'XSKT.VN', 'type' => 'text'],
            ['group' => 'general', 'key' => 'tagline', 'value' => 'Số chuẩn xác - May mắn phát', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_logo', 'value' => null, 'type' => 'image'],
            ['group' => 'general', 'key' => 'favicon', 'value' => null, 'type' => 'image'],
            ['group' => 'general', 'key' => 'apple_touch_icon', 'value' => null, 'type' => 'image'],

            // Tab 2: Meta Tags
            ['group' => 'meta', 'key' => 'title_template', 'value' => '{page_title} | {site_name}', 'type' => 'text'],
            ['group' => 'meta', 'key' => 'default_description', 'value' => 'Kết quả xổ số 3 miền nhanh nhất và chính xác nhất. XSMB, XSMT, XSMN hôm nay, thống kê, soi cầu, dò vé số.', 'type' => 'textarea'],
            ['group' => 'meta', 'key' => 'default_keywords', 'value' => 'xổ số, kết quả xổ số, XSMB, XSMT, XSMN, xổ số hôm nay, soi cầu, thống kê', 'type' => 'textarea'],
            ['group' => 'meta', 'key' => 'meta_author', 'value' => 'XSKT.VN', 'type' => 'text'],
            ['group' => 'meta', 'key' => 'robots_index', 'value' => '1', 'type' => 'toggle'],
            ['group' => 'meta', 'key' => 'robots_follow', 'value' => '1', 'type' => 'toggle'],

            // Tab 3: Open Graph
            ['group' => 'og', 'key' => 'default_image', 'value' => null, 'type' => 'image'],
            ['group' => 'og', 'key' => 'site_name', 'value' => null, 'type' => 'text'],
            ['group' => 'og', 'key' => 'type', 'value' => 'website', 'type' => 'select'],
            ['group' => 'og', 'key' => 'locale', 'value' => 'vi_VN', 'type' => 'text'],

            // Tab 4: Twitter Card
            ['group' => 'twitter', 'key' => 'card_type', 'value' => 'summary_large_image', 'type' => 'select'],
            ['group' => 'twitter', 'key' => 'site_handle', 'value' => null, 'type' => 'text'],
            ['group' => 'twitter', 'key' => 'default_image', 'value' => null, 'type' => 'image'],

            // Tab 5: Analytics & Scripts
            ['group' => 'analytics', 'key' => 'google_search_console', 'value' => null, 'type' => 'text'],
            ['group' => 'analytics', 'key' => 'bing_webmaster', 'value' => null, 'type' => 'text'],
            ['group' => 'analytics', 'key' => 'ga4_id', 'value' => null, 'type' => 'text'],
            ['group' => 'analytics', 'key' => 'gtm_id', 'value' => null, 'type' => 'text'],
            ['group' => 'analytics', 'key' => 'facebook_pixel_id', 'value' => null, 'type' => 'text'],
            ['group' => 'analytics', 'key' => 'custom_head_scripts', 'value' => null, 'type' => 'code'],
            ['group' => 'analytics', 'key' => 'custom_body_scripts', 'value' => null, 'type' => 'code'],
            ['group' => 'analytics', 'key' => 'custom_footer_scripts', 'value' => null, 'type' => 'code'],

            // Tab 6: Schema.org
            ['group' => 'schema', 'key' => 'org_name', 'value' => 'XSKT.VN', 'type' => 'text'],
            ['group' => 'schema', 'key' => 'org_logo', 'value' => null, 'type' => 'image'],
            ['group' => 'schema', 'key' => 'org_url', 'value' => null, 'type' => 'text'],
            ['group' => 'schema', 'key' => 'contact_email', 'value' => null, 'type' => 'text'],
            ['group' => 'schema', 'key' => 'contact_phone', 'value' => null, 'type' => 'text'],

            // Tab 7: Footer
            ['group' => 'footer', 'key' => 'about_text', 'value' => 'Trang web cung cấp kết quả xổ số 3 miền nhanh nhất và chính xác nhất', 'type' => 'textarea'],
            ['group' => 'footer', 'key' => 'copyright_template', 'value' => '© {year} {site_name}', 'type' => 'text'],
            ['group' => 'footer', 'key' => 'disclaimer_text', 'value' => 'Kết quả chỉ mang tính chất tham khảo', 'type' => 'textarea'],

            // Tab 8: Advanced
            ['group' => 'advanced', 'key' => 'robots_txt', 'value' => "User-agent: *\nDisallow:", 'type' => 'code'],
            ['group' => 'advanced', 'key' => 'canonical_prefix', 'value' => null, 'type' => 'text'],
            ['group' => 'advanced', 'key' => 'trailing_slash', 'value' => '0', 'type' => 'toggle'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                ['value' => $setting['value'], 'type' => $setting['type']]
            );
        }

        $this->command->info('Site settings seeded successfully!');
    }
}
