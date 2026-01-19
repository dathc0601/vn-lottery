@extends('layouts.app')

@section('title', $province->name . ' - Kết quả xổ số')

@section('page-content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex text-sm text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-[#4a7c2c]">Trang chủ</a>
        <span class="mx-2">/</span>
        @if($province->region == 'north')
            <a href="{{ route('xsmb') }}" class="hover:text-[#4a7c2c]">XSMB</a>
        @elseif($province->region == 'central')
            <a href="{{ route('xsmt') }}" class="hover:text-[#4a7c2c]">XSMT</a>
        @else
            <a href="{{ route('xsmn') }}" class="hover:text-[#4a7c2c]">XSMN</a>
        @endif
        <span class="mx-2">/</span>
        <span class="text-gray-800 font-medium">{{ $province->name }}</span>
    </nav>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-xl p-6 shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $province->name }}</h1>
                <div class="flex items-center gap-4 text-sm">
                    <span class="bg-white/20 px-3 py-1 rounded-full">
                        {{ $province->region == 'north' ? 'Miền Bắc' : ($province->region == 'central' ? 'Miền Trung' : 'Miền Nam') }}
                    </span>
                    <span>Mã: <strong>{{ strtoupper($province->code) }}</strong></span>
                    <span>Giờ quay: <strong>{{ \Carbon\Carbon::parse($province->draw_time)->format('H:i') }}</strong></span>
                </div>
            </div>
            <div class="hidden md:block">
                <svg class="w-16 h-16 text-white/30" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Draw Days Info -->
    @if($province->draw_days && count($province->draw_days) > 0)
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="text-sm font-medium text-blue-900">Lịch quay xổ số:</p>
                <p class="text-sm text-blue-800 mt-1">
                    @php
                        $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
                        $drawDays = array_map(fn($d) => $days[$d], $province->draw_days);
                    @endphp
                    <strong>{{ implode(', ', $drawDays) }}</strong>
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tổng kết quả</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $results->total() }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Kết quả mới nhất</p>
                    <p class="text-lg font-bold text-gray-800">
                        {{ $results->first() ? $results->first()->draw_date->format('d/m/Y') : 'Chưa có' }}
                    </p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-red-500 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Giải ĐB gần nhất</p>
                    <p class="text-lg font-bold text-red-600">
                        {{ $results->first()->prize_special ?? 'N/A' }}
                    </p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Results List -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-[#4a7c2c]" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
            </svg>
            Lịch sử kết quả
        </h2>

        @if($results->count() > 0)
            <div class="space-y-4">
                @foreach($results as $result)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-3 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="font-bold text-gray-800">
                                    {{ $result->draw_date->format('d/m/Y') }} - {{ $result->turn_num }}
                                </h3>
                                <span class="text-sm text-gray-600">
                                    {{ $result->draw_time->format('H:i') }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <table class="result-table w-full">
                                <tbody>
                                    <tr class="bg-red-50">
                                        <td class="prize-label w-1/4">Giải ĐB</td>
                                        <td class="prize-special text-2xl">{{ $result->prize_special }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Nhất</td>
                                        <td class="text-lg font-bold text-blue-700">{{ $result->prize_1 }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Nhì</td>
                                        <td class="font-semibold">{{ str_replace(',', ' - ', $result->prize_2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Ba</td>
                                        <td class="text-sm">{{ str_replace(',', ' - ', $result->prize_3) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Tư</td>
                                        <td class="text-sm">{{ str_replace(',', ' - ', $result->prize_4) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Năm</td>
                                        <td class="text-sm">{{ str_replace(',', ' - ', $result->prize_5) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Sáu</td>
                                        <td class="text-sm">{{ str_replace(',', ' - ', $result->prize_6) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="prize-label">Giải Bảy</td>
                                        <td class="text-sm">{{ str_replace(',', ' - ', $result->prize_7) }}</td>
                                    </tr>
                                    @if($result->prize_8)
                                    <tr>
                                        <td class="prize-label">Giải Tám</td>
                                        <td class="text-sm">{{ $result->prize_8 }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $results->links() }}
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                <svg class="w-12 h-12 text-yellow-500 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <p class="text-yellow-800 font-medium">Chưa có kết quả cho tỉnh này</p>
            </div>
        @endif
    </div>
</div>
@endsection
