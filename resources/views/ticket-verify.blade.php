@extends('layouts.app')

@section('title', 'Dò Vé Số - Kiểm tra trúng thưởng')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-xl p-6 shadow-lg">
        <h1 class="text-3xl font-bold mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2H6zm1 2a1 1 0 000 2h6a1 1 0 100-2H7zm6 7a1 1 0 011 1v3a1 1 0 11-2 0v-3a1 1 0 011-1zm-3 3a1 1 0 100 2h.01a1 1 0 100-2H10zm-4 1a1 1 0 011-1h.01a1 1 0 110 2H7a1 1 0 01-1-1zm1-4a1 1 0 100 2h.01a1 1 0 100-2H7zm2 1a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zm4-4a1 1 0 100 2h.01a1 1 0 100-2H13zM9 9a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1zM7 8a1 1 0 000 2h.01a1 1 0 000-2H7z" clip-rule="evenodd" />
            </svg>
            Dò Vé Số Trúng Thưởng
        </h1>
        <p class="text-green-100">Kiểm tra vé số của bạn có trúng giải không</p>
    </div>

    <!-- Verification Form -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <form method="POST" action="{{ route('ticket.verify') }}" class="space-y-4">
            @csrf

            <div class="grid md:grid-cols-2 gap-4">
                <!-- Date Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Ngày quay thưởng
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="draw_date"
                           value="{{ $selectedDate ? $selectedDate->format('Y-m-d') : '' }}"
                           max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent @error('draw_date') border-red-500 @enderror">
                    @error('draw_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Province Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Tỉnh/Thành phố
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="province_id" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent @error('province_id') border-red-500 @enderror">
                        <option value="">-- Chọn tỉnh --</option>
                        @foreach($provinces->groupBy('region') as $regionName => $regionProvinces)
                            <optgroup label="{{ $regionName == 'north' ? 'Miền Bắc' : ($regionName == 'central' ? 'Miền Trung' : 'Miền Nam') }}">
                                @foreach($regionProvinces as $province)
                                    <option value="{{ $province->id }}" {{ $selectedProvinceId == $province->id ? 'selected' : '' }}>
                                        {{ $province->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('province_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ticket Number Input -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Số vé cần dò (2-6 chữ số cuối)
                    <span class="text-red-500">*</span>
                </label>
                <input type="text" name="ticket_number"
                       value="{{ $ticketNumber }}"
                       placeholder="Nhập 2-6 chữ số cuối của vé số"
                       maxlength="6"
                       pattern="[0-9]{2,6}"
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent text-2xl font-bold text-center @error('ticket_number') border-red-500 @enderror">
                @error('ticket_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-600">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    Nhập 2-6 chữ số cuối của vé số. Ví dụ: vé số 123456 thì nhập "56" hoặc "456" hoặc "3456"...
                </p>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full px-6 py-3 bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-lg hover:from-[#3a6020] hover:to-[#5a8c3c] transition-all duration-200 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
                Kiểm Tra Vé Số
            </button>
        </form>
    </div>

    <!-- Results Display -->
    @if($result !== null)
        @if(count($matchedPrizes) > 0)
            <!-- Winning Result -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-400 rounded-xl overflow-hidden shadow-lg animate-pulse-slow">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-6 text-center">
                    <svg class="w-16 h-16 mx-auto mb-3" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <h2 class="text-3xl font-bold">🎉 CHÚC MỪNG! VÉ CỦA BẠN ĐÃ TRÚNG GIẢI! 🎉</h2>
                    <p class="text-green-100 mt-2">Vé số <strong class="text-2xl">{{ $ticketNumber }}</strong> đã trúng {{ count($matchedPrizes) }} giải</p>
                </div>

                <div class="p-6 space-y-4">
                    @foreach($matchedPrizes as $match)
                        <div class="bg-white rounded-lg p-4 border-2 border-green-300 shadow-md">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-bold mb-2">
                                        {{ $match['tier'] }}
                                    </span>
                                    <p class="text-2xl font-bold text-gray-800">{{ $match['number'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Giá trị giải thưởng</p>
                                    <p class="text-xl font-bold text-green-600">{{ $match['amount'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 pb-6">
                    <div class="bg-yellow-50 border border-yellow-300 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1 a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-yellow-800">
                                <p class="font-medium">Lưu ý quan trọng:</p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>Vui lòng giữ vé gốc cẩn thận</li>
                                    <li>Liên hệ đại lý xổ số hoặc công ty xổ số kiến thiết để nhận thưởng</li>
                                    <li>Thời hạn nhận thưởng: 60 ngày kể từ ngày quay số</li>
                                    <li>Mang theo CMND/CCCD khi đến nhận thưởng</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- No Match Result -->
            <div class="bg-gray-50 border-2 border-gray-300 rounded-xl overflow-hidden shadow-lg">
                <div class="bg-gradient-to-r from-gray-600 to-gray-700 text-white p-6 text-center">
                    <svg class="w-16 h-16 mx-auto mb-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <h2 class="text-2xl font-bold">Vé số không trúng giải</h2>
                    <p class="text-gray-200 mt-2">Vé số <strong class="text-xl">{{ $ticketNumber }}</strong> không khớp với bất kỳ giải nào</p>
                </div>

                <div class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium">Lời khuyên:</p>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <li>Kiểm tra lại thông tin vé số và ngày quay</li>
                                    <li>Đảm bảo bạn đã chọn đúng tỉnh/thành phố</li>
                                    <li>Thử dò với số chữ số khác (2-6 chữ số cuối)</li>
                                    <li>Chúc bạn may mắn lần sau!</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Result Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-3 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">
                    Kết quả {{ $result->province->name }} - {{ $result->draw_date->format('d/m/Y') }}
                </h3>
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
    @elseif($selectedDate && $selectedProvinceId && $result === null)
        <!-- No Result Found -->
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-lg font-medium text-yellow-800">Không tìm thấy kết quả</h3>
                    <p class="text-yellow-700 mt-1">
                        Không tìm thấy kết quả xổ số cho ngày {{ $selectedDate->format('d/m/Y') }}. Vui lòng kiểm tra lại ngày và tỉnh đã chọn.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
