@extends('layouts.app')

@section('title', 'Thống Kê Xổ Số - Công cụ phân tích kết quả xổ số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Thống kê</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content (65%) -->
        <div class="flex-1 lg:w-[100% - 275px]">
            <div class="space-y-4">
                <!-- Main Content Card -->
                <div class="bg-white rounded shadow overflow-hidden">
                    <!-- Orange Header -->
                    <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                        Giới thiệu tính năng Thống Kê
                    </div>

                    <!-- Content -->
                    <div class="p-4">
                        <!-- Introduction -->
                        <p class="text-gray-700 mb-4">
                            <strong>Thống Kê</strong> là bộ công cụ quan trọng, giúp người chơi xổ số phân tích và nghiên cứu kết quả một cách chính xác nhất.
                        </p>

                        <p class="text-gray-700 mb-6">
                            Tại <strong class="text-[#ff6600]">vn-lottery</strong>, chúng tôi tổng hợp các tính năng thống kê đa dạng, hỗ trợ bạn dễ dàng tra cứu, đánh giá xu hướng và chọn ra những con số tiềm năng, tối ưu cơ hội trúng thưởng!
                        </p>

                        <!-- Features List - Two Columns -->
                        <div class="grid md:grid-cols-2 gap-x-8 gap-y-2 mb-6">
                            <!-- Left Column -->
                            <div class="space-y-2">
                                <a href="{{ route('statistics.overdue') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Thống kê loto gan
                                </a>
                                <a href="{{ route('statistics.quick') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Thống kê nhanh
                                </a>
                                <a href="{{ route('statistics.frequency') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Thống kê tần suất loto
                                </a>
                                <a href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                                    <span class="text-gray-400 mr-2">▸</span>
                                    Tìm cầu loto
                                </a>
                                <a href="{{ route('statistics.important') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Thống kê quan trọng
                                </a>
                                <a href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                                    <span class="text-gray-400 mr-2">▸</span>
                                    Thống kê chu kỳ gan theo tỉnh
                                </a>
                                <a href="{{ route('statistics.weekly-special') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Bảng đặc biệt tuần
                                </a>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-2">
                                <a href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                                    <span class="text-gray-400 mr-2">▸</span>
                                    Thống kê chu kỳ dàn loto
                                </a>
                                <a href="{{ route('statistics.head-tail') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Thống kê đầu đuôi loto
                                </a>
                                <a href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                                    <span class="text-gray-400 mr-2">▸</span>
                                    Thống kê chu kỳ đặc biệt
                                </a>
                                <a href="{{ route('statistics.by-sum') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Thống kê theo tổng
                                </a>
                                <a href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                                    <span class="text-gray-400 mr-2">▸</span>
                                    Thống kê chu kỳ dài nhất
                                </a>
                                <a href="#" class="flex items-center text-gray-500 cursor-not-allowed">
                                    <span class="text-gray-400 mr-2">▸</span>
                                    Thống kê tần số nhịp loto
                                </a>
                                <a href="{{ route('statistics.monthly-special') }}" class="flex items-center text-gray-700 hover:text-[#ff6600] transition-colors">
                                    <span class="text-[#ff6600] mr-2">▸</span>
                                    Bảng đặc biệt tháng
                                </a>
                            </div>
                        </div>

                        <!-- Footer Note -->
                        <p class="text-sm text-gray-500 italic">
                            Đội ngũ kỹ thuật viên của vn-lottery không ngừng cập nhật, cải tiến và bổ sung nhiều tính năng hữu ích để mang đến trải nghiệm tốt nhất. Chúng tôi rất mong nhận được sự ủng hộ từ bạn!
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (35%) -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
            :showCalendar="true"
            :showProvinces="true"
        />
    </div>
</div>
@endsection
