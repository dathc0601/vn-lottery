<?php

return [
    'brand_name' => 'Quản trị XSKT.VN',

    // Navigation Groups
    'nav' => [
        'lottery_data' => 'Dữ liệu xổ số',
        'content' => 'Nội dung',
        'appearance' => 'Giao diện',
        'seo' => 'SEO',
        'system' => 'Hệ thống',
    ],

    // Common Terms
    'common' => [
        'active' => 'Hoạt động',
        'inactive' => 'Không hoạt động',
        'status' => 'Trạng thái',
        'name' => 'Tên',
        'code' => 'Mã',
        'date' => 'Ngày',
        'time' => 'Giờ',
        'province' => 'Tỉnh/Thành',
        'provinces' => 'Các tỉnh',
        'actions' => 'Hành động',
        'create' => 'Tạo mới',
        'edit' => 'Chỉnh sửa',
        'delete' => 'Xóa',
        'view' => 'Xem',
        'save' => 'Lưu',
        'cancel' => 'Hủy',
        'search' => 'Tìm kiếm',
        'filter' => 'Lọc',
    ],

    // Regions
    'regions' => [
        'north' => 'Miền Bắc',
        'central' => 'Miền Trung',
        'south' => 'Miền Nam',
        'north_full' => 'Miền Bắc',
        'central_full' => 'Miền Trung',
        'south_full' => 'Miền Nam',
    ],

    // Days of Week
    'days' => [
        'monday' => 'Thứ Hai',
        'tuesday' => 'Thứ Ba',
        'wednesday' => 'Thứ Tư',
        'thursday' => 'Thứ Năm',
        'friday' => 'Thứ Sáu',
        'saturday' => 'Thứ Bảy',
        'sunday' => 'Chủ Nhật',
    ],

    // Sidebar Settings
    'sidebar_settings' => [
        'title' => 'Cài đặt Sidebar',
        'description' => 'Chọn các tỉnh hiển thị trong sidebar trái',
        'show_in_sidebar' => 'Hiển thị trong Sidebar',
        'order' => 'Thứ tự',
        'saved' => 'Đã lưu cài đặt sidebar!',
    ],

    // SEO Settings
    'seo_settings' => [
        'title' => 'Cài đặt SEO',
        'saved' => 'Đã lưu cài đặt SEO!',
        'cache_cleared' => 'Đã xóa cache cài đặt!',
        'clear_cache' => 'Xóa cache',

        // Tab labels
        'tabs' => [
            'general' => 'Tổng quan',
            'meta' => 'Meta Tags',
            'og' => 'Open Graph',
            'twitter' => 'Twitter Card',
            'analytics' => 'Analytics & Scripts',
            'schema' => 'Schema.org',
            'footer' => 'Footer',
            'advanced' => 'Nâng cao',
        ],

        // General
        'site_name' => 'Tên website',
        'tagline' => 'Slogan / Tagline',
        'header_subtitle' => 'Phụ đề header (SEO)',
        'header_subtitle_help' => 'Dòng phụ đề hiển thị dưới tên website ở header. Để trống sẽ dùng tagline.',
        'site_logo' => 'Logo website',
        'favicon' => 'Favicon',
        'favicon_help' => 'Chấp nhận .ico, .png, .svg',
        'apple_touch_icon' => 'Apple Touch Icon',

        // Meta Tags
        'title_template' => 'Template tiêu đề',
        'title_template_help' => 'Sử dụng {page_title} và {site_name} làm biến',
        'default_description' => 'Mô tả mặc định',
        'default_keywords' => 'Từ khóa mặc định',
        'meta_author' => 'Tác giả',
        'robots_index' => 'Cho phép index',
        'robots_follow' => 'Cho phép follow',

        // Open Graph
        'og_default_image' => 'Ảnh OG mặc định',
        'og_default_image_help' => 'Khuyến nghị 1200x630px',
        'og_site_name' => 'OG Site Name',
        'og_site_name_help' => 'Để trống sẽ dùng tên website',
        'og_type' => 'OG Type',
        'og_locale' => 'OG Locale',

        // Twitter Card
        'twitter_card_type' => 'Loại Twitter Card',
        'twitter_site_handle' => 'Twitter @username',
        'twitter_default_image' => 'Ảnh Twitter mặc định',

        // Analytics & Scripts
        'google_search_console' => 'Google Search Console',
        'google_search_console_help' => 'Mã xác minh (chỉ nhập giá trị content)',
        'bing_webmaster' => 'Bing Webmaster',
        'bing_webmaster_help' => 'Mã xác minh (chỉ nhập giá trị content)',
        'ga4_id' => 'Google Analytics 4 ID',
        'ga4_id_help' => 'Ví dụ: G-XXXXXXXXXX',
        'gtm_id' => 'Google Tag Manager ID',
        'gtm_id_help' => 'Ví dụ: GTM-XXXXXXX',
        'facebook_pixel_id' => 'Facebook Pixel ID',
        'custom_head_scripts' => 'Script trong <head>',
        'custom_head_scripts_help' => 'Thêm code tùy chỉnh vào thẻ <head>',
        'custom_body_scripts' => 'Script sau <body>',
        'custom_body_scripts_help' => 'Thêm code sau thẻ mở <body>',
        'custom_footer_scripts' => 'Script trước </footer>',
        'custom_footer_scripts_help' => 'Thêm code trước thẻ đóng </footer>',

        // Schema.org
        'org_name' => 'Tên tổ chức',
        'org_logo' => 'Logo tổ chức',
        'org_url' => 'URL tổ chức',
        'contact_email' => 'Email liên hệ',
        'contact_phone' => 'Số điện thoại',

        // Footer
        'about_text' => 'Giới thiệu ngắn',
        'copyright_template' => 'Template bản quyền',
        'copyright_template_help' => 'Sử dụng {year} và {site_name} làm biến',
        'disclaimer_text' => 'Tuyên bố miễn trừ',

        // Advanced
        'robots_txt' => 'Nội dung robots.txt',
        'canonical_prefix' => 'Canonical URL prefix',
        'canonical_prefix_help' => 'Ví dụ: https://xskt.vn',
        'trailing_slash' => 'Thêm dấu / cuối URL',
    ],

    // Footer Manager
    'footer_manager' => [
        'title' => 'Quản lý Footer',
        'columns' => 'Các cột Footer',
        'links' => 'Liên kết',
        'add_column' => 'Thêm cột mới',
        'edit_column' => 'Chỉnh sửa cột',
        'add_link' => 'Thêm liên kết',
        'edit_link' => 'Chỉnh sửa liên kết',
        'no_columns' => 'Chưa có cột footer nào',
        'no_links' => 'Chưa có liên kết nào',
        'show_preview' => 'Xem trước',
        'hide_preview' => 'Ẩn xem trước',
        'preview' => 'Xem trước Footer',
        'clear_cache' => 'Xóa cache',
        'cache_cleared' => 'Đã xóa cache footer!',
        'created_column' => 'Đã tạo cột footer mới!',
        'updated_column' => 'Đã cập nhật cột footer!',
        'deleted_column' => 'Đã xóa cột footer!',
        'created_link' => 'Đã tạo liên kết mới!',
        'updated_link' => 'Đã cập nhật liên kết!',
        'deleted_link' => 'Đã xóa liên kết!',
        'not_found' => 'Không tìm thấy!',
        'activated' => 'Đã kích hoạt!',
        'deactivated' => 'Đã vô hiệu hóa!',
        'confirm_delete' => 'Bạn có chắc chắn muốn xóa?',
        'text_settings' => 'Cài đặt nội dung Footer',
        'text_settings_saved' => 'Đã lưu cài đặt nội dung footer!',
        'extended_settings' => 'Cài đặt Footer mở rộng',
        'extended_settings_saved' => 'Đã lưu cài đặt footer mở rộng!',
        'extended' => [
            'intro_section' => 'Phần giới thiệu',
            'intro_title' => 'Tiêu đề giới thiệu',
            'intro_text' => 'Nội dung giới thiệu',
            'info_table_section' => 'Bảng thông tin',
            'info_table_rows' => 'Các dòng thông tin',
            'info_label' => 'Nhãn',
            'info_value' => 'Giá trị',
            'notes_section' => 'Ghi chú',
            'notes' => 'Danh sách ghi chú',
            'note_text' => 'Nội dung ghi chú',
            'reference_links_section' => 'Liên kết tham khảo',
            'reference_links' => 'Danh sách liên kết',
            'link_label' => 'Nhãn hiển thị',
            'link_url' => 'URL',
            'link_new_tab' => 'Mở tab mới',
            'display_section' => 'Hiển thị',
            'show_schedule' => 'Hiển thị bảng lịch mở thưởng',
            'show_bottom_nav' => 'Hiển thị thanh navigation dưới',
        ],
        'fields' => [
            'title' => 'Tiêu đề',
            'type' => 'Loại cột',
            'column_type_links' => 'Danh sách liên kết',
            'column_type_about' => 'Giới thiệu',
            'column_type_info' => 'Thông tin',
            'label' => 'Nhãn hiển thị',
            'link_type' => 'Loại liên kết',
            'link_type_route' => 'Route (Laravel)',
            'link_type_url' => 'URL bên ngoài',
            'route_name' => 'Tên Route',
            'url' => 'URL',
            'open_in_new_tab' => 'Mở tab mới',
            'is_active' => 'Kích hoạt',
            'column' => 'Cột',
            'about_text' => 'Giới thiệu ngắn',
            'copyright_template' => 'Template bản quyền',
            'copyright_template_help' => 'Sử dụng {year} và {site_name} làm biến',
            'disclaimer_text' => 'Tuyên bố miễn trừ',
        ],
    ],

    // SEO Overrides
    'seo_overrides' => [
        'title' => 'SEO theo trang',
        'label' => 'SEO theo trang',
        'singular' => 'SEO Override',

        // Sections
        'section_path' => 'Đường dẫn & Kiểu so khớp',
        'section_meta' => 'Meta Tags',
        'section_og' => 'Open Graph',
        'section_schema' => 'Schema JSON-LD',

        // Fields
        'fields' => [
            'label' => 'Tên gợi nhớ',
            'label_help' => 'Tên hiển thị trong admin, ví dụ: Trang XSMB',
            'path_pattern' => 'Đường dẫn (path)',
            'path_pattern_help' => 'Ví dụ: /xsmb hoặc /xsmb/*',
            'match_type' => 'Kiểu so khớp',
            'match_type_exact' => 'Chính xác',
            'match_type_wildcard' => 'Wildcard (*)',
            'priority' => 'Độ ưu tiên',
            'priority_help' => 'Số lớn hơn = ưu tiên cao hơn',
            'is_active' => 'Kích hoạt',
            'page_title' => 'Tiêu đề trang',
            'meta_description' => 'Mô tả (meta description)',
            'meta_keywords' => 'Từ khóa (meta keywords)',
            'robots' => 'Robots',
            'robots_help' => 'Ví dụ: noindex, follow',
            'canonical_url' => 'Canonical URL',
            'og_title' => 'OG Title',
            'og_description' => 'OG Description',
            'og_image' => 'OG Image URL',
            'og_image_help' => 'URL đầy đủ đến ảnh OG',
            'schema_jsonld' => 'JSON-LD Schema',
            'schema_jsonld_help' => 'Nhập JSON-LD hợp lệ',
        ],

        // Filters
        'filters' => [
            'match_type' => 'Kiểu so khớp',
            'is_active' => 'Trạng thái',
        ],
    ],

    // Navigation Management
    'navigation' => [
        'title' => 'Quản lý Navigation',
        'items' => 'Các mục Navigation',
        'add_item' => 'Thêm mục mới',
        'edit_item' => 'Chỉnh sửa mục',
        'no_items' => 'Chưa có mục navigation nào',
        'show_preview' => 'Xem trước',
        'hide_preview' => 'Ẩn xem trước',
        'preview' => 'Xem trước Navigation',
        'clear_cache' => 'Xóa cache',
        'cache_cleared' => 'Đã xóa cache navigation!',
        'created' => 'Đã tạo mục navigation mới!',
        'updated' => 'Đã cập nhật mục navigation!',
        'deleted' => 'Đã xóa mục navigation!',
        'not_found' => 'Không tìm thấy mục navigation!',
        'activated' => 'Đã kích hoạt mục navigation!',
        'deactivated' => 'Đã vô hiệu hóa mục navigation!',
        'confirm_delete' => 'Bạn có chắc chắn muốn xóa mục này?',
        'add_child' => 'Thêm mục con',
        'fields' => [
            'title' => 'Tiêu đề',
            'title_short' => 'Tiêu đề ngắn',
            'title_short_help' => 'Hiển thị trên mobile (không bắt buộc)',
            'type' => 'Loại',
            'route_name' => 'Tên Route',
            'url' => 'URL',
            'active_pattern' => 'Pattern kích hoạt',
            'dropdown_type' => 'Loại dropdown',
            'icon' => 'Icon',
            'parent' => 'Mục cha',
            'no_parent' => 'Không có (Mục gốc)',
            'is_active' => 'Kích hoạt',
            'open_in_new_tab' => 'Mở tab mới',
        ],
    ],
];
