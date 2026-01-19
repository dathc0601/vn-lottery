<?php

return [
    'title' => 'Các tỉnh',
    'singular' => 'Tỉnh',

    'sections' => [
        'basic_info' => 'Thông tin cơ bản',
        'draw_schedule' => 'Lịch quay thưởng',
        'settings' => 'Cài đặt',
    ],

    'fields' => [
        'name' => 'Tên tỉnh',
        'code' => 'Mã API',
        'slug' => 'Slug',
        'region' => 'Vùng miền',
        'draw_time' => 'Giờ quay',
        'draw_days' => 'Ngày quay',
        'sort_order' => 'Thứ tự hiển thị',
        'is_active' => 'Hoạt động',
    ],

    'filters' => [
        'region' => 'Vùng miền',
        'active_status' => 'Trạng thái hoạt động',
    ],

    'actions' => [
        'fetch_now' => 'Lấy dữ liệu ngay',
        'fetch_dispatched' => 'Đã gửi lệnh lấy dữ liệu',
        'fetching_for' => 'Đang lấy kết quả xổ số cho :name',
    ],
];
