@extends('layouts.app')

@section('title', 'Vietlott - Xổ Số Điện Toán')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-xl p-6 shadow-lg">
        <h1 class="text-3xl font-bold mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
            </svg>
            Vietlott - Xổ Số Điện Toán
        </h1>
        <p class="text-green-100">Kết quả xổ số Vietlott - Mega 6/45, Power 6/55, Max 3D, Max 3D Pro</p>
    </div>

    <!-- Coming Soon Notice -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-300 rounded-xl overflow-hidden shadow-lg">
        <div class="p-8 text-center">
            <svg class="w-24 h-24 mx-auto text-blue-600 mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
            </svg>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Tính năng đang được phát triển</h2>
            <p class="text-gray-600 text-lg mb-6">
                Chúng tôi đang hoàn thiện tính năng hiển thị kết quả Vietlott. <br>
                Vui lòng quay lại sau!
            </p>
            <div class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                </svg>
                Sắp ra mắt
            </div>
        </div>
    </div>

    <!-- Games Preview -->
    <div class="grid md:grid-cols-2 gap-6">
        @foreach($games as $game)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow">
                <!-- Game Header -->
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white p-6">
                    <h3 class="text-2xl font-bold mb-1">{{ $game['name'] }}</h3>
                    <p class="text-purple-100 text-sm">{{ $game['description'] }}</p>
                </div>

                <!-- Game Content -->
                <div class="p-6">
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Jackpot hiện tại</span>
                            <span class="text-xs text-gray-500">Dự kiến</span>
                        </div>
                        <div class="bg-gradient-to-r from-yellow-100 to-orange-100 rounded-lg p-4 border-2 border-yellow-400">
                            <p class="text-center text-3xl font-bold text-orange-700">
                                <svg class="w-6 h-6 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                                </svg>
                                Đang cập nhật
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>Lịch quay:</strong> Đang cập nhật</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>Giờ quay:</strong> Đang cập nhật</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                            </svg>
                            <span><strong>Giá vé:</strong> Đang cập nhật</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button class="w-full px-4 py-2 bg-gray-300 text-gray-600 rounded-lg font-medium cursor-not-allowed" disabled>
                            Xem kết quả (Sắp có)
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div>
                <h3 class="text-lg font-bold text-blue-900 mb-2">Về Vietlott</h3>
                <div class="text-sm text-blue-800 space-y-2">
                    <p>
                        <strong>Vietlott</strong> là hệ thống xổ số tự chọn điện toán đầu tiên tại Việt Nam, được vận hành bởi
                        Công ty Xổ số Điện toán Việt Nam (Vietlott).
                    </p>
                    <p><strong>Các sản phẩm chính:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-4">
                        <li><strong>Mega 6/45:</strong> Giải Jackpot 1 khởi điểm 12 tỷ đồng, quay 3 lần/tuần</li>
                        <li><strong>Power 6/55:</strong> Giải Jackpot khởi điểm 30 tỷ đồng, quay 3 lần/tuần</li>
                        <li><strong>Max 3D:</strong> Quay hàng ngày, giải nhất 3 tỷ đồng</li>
                        <li><strong>Max 3D Pro:</strong> Phiên bản nâng cao của Max 3D</li>
                    </ul>
                    <p class="mt-3">
                        <strong>Lưu ý:</strong> Tính năng hiển thị kết quả Vietlott đang được phát triển và sẽ sớm được cập nhật.
                        Hiện tại bạn có thể xem kết quả xổ số truyền thống (XSMB, XSMT, XSMN) tại các trang khác của website.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid md:grid-cols-3 gap-4">
        <a href="{{ route('xsmb') }}" class="block bg-white rounded-lg p-6 shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">XSMB</h4>
                    <p class="text-sm text-gray-600">Xổ Số Miền Bắc</p>
                </div>
            </div>
        </a>

        <a href="{{ route('xsmt') }}" class="block bg-white rounded-lg p-6 shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">XSMT</h4>
                    <p class="text-sm text-gray-600">Xổ Số Miền Trung</p>
                </div>
            </div>
        </a>

        <a href="{{ route('xsmn') }}" class="block bg-white rounded-lg p-6 shadow hover:shadow-lg transition-shadow border border-gray-200">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">XSMN</h4>
                    <p class="text-sm text-gray-600">Xổ Số Miền Nam</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
