@extends('layouts.app')

@section('title', 'Sổ Kết Quả - Xem lại kết quả xổ số')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-xl p-6 shadow-lg">
        <h1 class="text-3xl font-bold mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z" />
            </svg>
            Sổ Kết Quả Xổ Số
        </h1>
        <p class="text-green-100">Tra cứu lịch sử kết quả xổ số theo thời gian và tỉnh thành</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <form method="GET" action="{{ route('results.book') }}" class="space-y-4">
            <!-- Quick Period Selector -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn thời gian</label>
                <div class="flex flex-wrap gap-2">
                    @foreach(['30' => '30 ngày', '60' => '60 ngày', '90' => '90 ngày', '100' => '100 ngày', '200' => '200 ngày', '300' => '300 ngày', '500' => '500 ngày'] as $days => $label)
                        <button type="submit" name="period" value="{{ $days }}"
                                class="px-4 py-2 rounded-lg border-2 transition-all duration-200
                                       {{ $period == $days ? 'bg-[#4a7c2c] text-white border-[#4a7c2c]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#4a7c2c] hover:text-[#4a7c2c]' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Custom Date Range -->
            <div class="grid md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                           max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                           max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                </div>
            </div>

            <!-- Province and Region Filters -->
            <div class="grid md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Miền</label>
                    <select name="region" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                        <option value="">Tất cả miền</option>
                        <option value="north" {{ $region == 'north' ? 'selected' : '' }}>Miền Bắc</option>
                        <option value="central" {{ $region == 'central' ? 'selected' : '' }}>Miền Trung</option>
                        <option value="south" {{ $region == 'south' ? 'selected' : '' }}>Miền Nam</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tỉnh thành</label>
                    <select name="province_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                        <option value="">Tất cả tỉnh</option>
                        @foreach($provinces->groupBy('region') as $regionName => $regionProvinces)
                            <optgroup label="{{ $regionName == 'north' ? 'Miền Bắc' : ($regionName == 'central' ? 'Miền Trung' : 'Miền Nam') }}">
                                @foreach($regionProvinces as $province)
                                    <option value="{{ $province->id }}" {{ $provinceId == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-6 py-3 bg-[#4a7c2c] text-white rounded-lg hover:bg-[#5a8c3c] transition-colors font-semibold shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    Tìm kiếm
                </button>
                <a href="{{ route('results.book') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold">
                    Đặt lại
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics Summary -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Tổng kết quả</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalResults }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-green-500 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Số tỉnh</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $provinceCount }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg p-4 border-l-4 border-purple-500 shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Khoảng thời gian</p>
                    <p class="text-lg font-bold text-gray-800">{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Display -->
    @if($results->count() > 0)
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg class="w-6 h-6 mr-2 text-[#4a7c2c]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 0l-2 2a1 1 0 101.414 1.414L8 10.414l1.293 1.293a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Kết quả tìm kiếm ({{ $results->total() }})
            </h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($results as $result)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white p-4">
                            <h3 class="font-bold text-lg">{{ $result->province->name }}</h3>
                            <div class="flex items-center justify-between mt-1">
                                <span class="text-sm text-green-100">{{ $result->draw_date->format('d/m/Y') }}</span>
                                <span class="text-xs text-green-100">{{ $result->draw_time->format('H:i') }}</span>
                            </div>
                        </div>

                        <!-- Prizes Preview -->
                        <div class="p-4 space-y-2">
                            <!-- Special Prize -->
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 rounded-lg p-3 border border-red-200">
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-700 block mb-1">Giải ĐB</span>
                                    <span class="prize-special text-xl">{{ $result->prize_special }}</span>
                                </div>
                            </div>

                            <!-- First Prize -->
                            <div class="bg-blue-50 rounded-lg p-2 border border-blue-200">
                                <div class="text-center">
                                    <span class="text-xs font-semibold text-gray-700 block mb-1">Giải Nhất</span>
                                    <span class="text-base font-bold text-blue-700">{{ $result->prize_1 }}</span>
                                </div>
                            </div>

                            <!-- Other Prizes (Compact) -->
                            <div class="text-xs text-gray-600 space-y-1">
                                <div class="flex justify-between">
                                    <span class="font-semibold">G2:</span>
                                    <span>{{ str_replace(',', ', ', $result->prize_2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold">G3:</span>
                                    <span>{{ Str::limit(str_replace(',', ', ', $result->prize_3), 20) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- View Details Button -->
                        <div class="px-4 pb-4">
                            <a href="{{ route('province.detail', ['region' => $result->province->region == 'north' ? 'xsmb' : ($result->province->region == 'central' ? 'xsmt' : 'xsmn'), 'slug' => $result->province->slug]) }}"
                               class="block w-full text-center px-4 py-2 bg-[#4a7c2c] text-white rounded-lg hover:bg-[#5a8c3c] transition-colors text-sm font-medium">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $results->links() }}
            </div>
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-lg font-medium text-yellow-800">Không tìm thấy kết quả</h3>
                    <p class="text-yellow-700 mt-1">
                        Không có kết quả nào trong khoảng thời gian đã chọn. Vui lòng thử lại với bộ lọc khác.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
