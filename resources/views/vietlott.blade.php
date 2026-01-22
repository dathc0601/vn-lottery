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

    <!-- Games Grid -->
    <div class="grid md:grid-cols-2 gap-6">
        @foreach($games as $gameType => $game)
            <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
                <!-- Game Header -->
                <div class="@if($gameType === 'mega645') bg-gradient-to-r from-purple-600 to-indigo-600
                            @elseif($gameType === 'power655') bg-gradient-to-r from-red-600 to-pink-600
                            @elseif($gameType === 'max3d') bg-gradient-to-r from-blue-600 to-cyan-600
                            @else bg-gradient-to-r from-amber-600 to-orange-600
                            @endif text-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold">{{ $game['name'] }}</h3>
                            <p class="text-sm opacity-90">{{ $game['description'] }}</p>
                        </div>
                        <div class="text-right text-xs opacity-80">
                            <div>{{ $game['schedule'] }}</div>
                            <div>{{ $game['draw_time'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Game Content -->
                <div class="p-4">
                    @if($game['has_data'] && $game['latest'])
                        <!-- Latest Result -->
                        <div class="mb-4 p-4 bg-gray-50 rounded-lg border">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-600">
                                    Kỳ quay: #{{ $game['latest']->draw_number }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $game['latest']->draw_date->format('d/m/Y') }}
                                </span>
                            </div>

                            <!-- Winning Numbers -->
                            @if(in_array($gameType, ['mega645', 'power655']))
                                <div class="flex flex-wrap justify-center gap-2">
                                    @foreach($game['latest']->winning_numbers as $number)
                                        <span class="w-10 h-10 flex items-center justify-center rounded-full text-white font-bold text-sm
                                            @if($gameType === 'mega645') bg-purple-500
                                            @else bg-red-500 @endif">
                                            {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                {{-- Max 3D / Max 3D Pro - nested prize structure --}}
                                <div class="space-y-2 text-sm">
                                    @foreach($game['latest']->winning_numbers as $prizeName => $numbers)
                                        <div class="flex items-center gap-2">
                                            <span class="w-24 text-gray-600 font-medium text-xs">{{ $prizeName }}:</span>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($numbers as $number)
                                                    <span class="px-2 py-1 bg-blue-500 text-white rounded font-bold text-xs">
                                                        {{ str_pad($number, 3, '0', STR_PAD_LEFT) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($game['latest']->jackpot_amount > 0)
                                <div class="mt-3 text-center">
                                    <span class="text-sm text-gray-600">Jackpot:</span>
                                    <span class="text-lg font-bold text-orange-600">
                                        {{ number_format($game['latest']->jackpot_amount) }} VND
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Results History -->
                        @if($game['results']->count() > 1)
                            <div class="border-t pt-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Kết quả gần đây</h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    @foreach($game['results']->skip(1) as $result)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-500">{{ $result->draw_date->format('d/m') }}</span>
                                                <span class="text-gray-400">#{{ $result->draw_number }}</span>
                                            </div>
                                            <div class="flex flex-wrap gap-1">
                                                @if(in_array($gameType, ['mega645', 'power655']))
                                                    @foreach($result->winning_numbers as $number)
                                                        <span class="w-6 h-6 flex items-center justify-center rounded-full text-white text-xs
                                                            @if($gameType === 'mega645') bg-purple-400
                                                            @else bg-red-400 @endif">
                                                            {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    {{-- Max 3D - show only special prize for history --}}
                                                    @php $specialPrize = $result->winning_numbers['Giải Đặc biệt'] ?? []; @endphp
                                                    @foreach($specialPrize as $number)
                                                        <span class="px-1.5 py-0.5 bg-blue-400 text-white rounded text-xs">
                                                            {{ str_pad($number, 3, '0', STR_PAD_LEFT) }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <!-- No Data State -->
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <p class="text-gray-500 mb-2">Chưa có dữ liệu</p>
                            <p class="text-sm text-gray-400">Dữ liệu sẽ được cập nhật tự động</p>
                        </div>
                    @endif

                    <!-- Game Info Footer -->
                    <div class="mt-4 pt-4 border-t flex items-center justify-between text-sm text-gray-500">
                        <span>Giá vé: {{ $game['ticket_price'] }}đ</span>
                        <span>{{ $game['schedule'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
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
                        <li><strong>Mega 6/45:</strong> Giải Jackpot 1 khởi điểm 12 tỷ đồng, quay Thứ 4, Thứ 6, Chủ nhật</li>
                        <li><strong>Power 6/55:</strong> Giải Jackpot khởi điểm 30 tỷ đồng, quay Thứ 3, Thứ 5, Thứ 7</li>
                        <li><strong>Max 3D:</strong> Quay hàng ngày, giải nhất 3 tỷ đồng</li>
                        <li><strong>Max 3D Pro:</strong> Phiên bản nâng cao của Max 3D, quay Thứ 2, Thứ 4, Thứ 6</li>
                    </ul>
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
