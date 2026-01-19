@extends('layouts.app-three-column')

@section('title', 'XSMB - Kết Quả Xổ Số Miền Bắc - SXMB Hôm Nay')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">XSMB</span>
@endsection

@section('left-sidebar')
    <x-left-sidebar />
@endsection

@section('page-content')
    <!-- Date Selector -->
    <div class="sidebar-section mb-4">
        <div class="sidebar-header">Chọn ngày xem kết quả</div>
        <div class="p-3">
            <form method="GET" action="{{ route('xsmb') }}" class="flex items-center gap-2">
                <input type="date"
                       name="date"
                       value="{{ $date->format('Y-m-d') }}"
                       max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                       class="flex-1 px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
                <button type="submit"
                        class="px-4 py-2 bg-[#ff6600] text-white rounded hover:bg-[#ff7700] transition-colors font-medium text-sm">
                    Xem
                </button>
                <a href="{{ route('xsmb') }}"
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors font-medium text-sm">
                    Hôm nay
                </a>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    @if($provinces->count() > 0)
        <div class="bg-blue-50 border border-blue-200 p-3 mb-4 rounded">
            <p class="text-sm text-blue-800">
                <strong>{{ $provinces->count() }} tỉnh</strong> quay ngày {{ $date->format('d/m/Y') }}:
                <span class="font-semibold">{{ $provinces->pluck('name')->join(', ') }}</span>
            </p>
        </div>
    @endif

    <!-- Results Display -->
    @if(count($results) > 0)
        @foreach($results as $result)
            <x-result-card-xskt :result="$result" region="xsmb" />
        @endforeach

        <!-- Load More Button -->
        <div class="text-center mt-6 mb-4">
            <button class="bg-[#ff6600] text-white px-8 py-3 rounded hover:bg-[#ff7700] transition-colors font-medium">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Xem thêm KQXS MB
            </button>
        </div>
    @else
        <div class="border border-yellow-400 bg-yellow-50 px-4 py-3 rounded mb-4">
            <p class="font-semibold text-yellow-800">Chưa có kết quả</p>
            <p class="text-sm text-yellow-700 mt-1">
                Không tìm thấy kết quả cho ngày {{ $date->format('d/m/Y') }}.
                @if($provinces->count() > 0)
                    <br>Các tỉnh quay ngày này: <strong>{{ $provinces->pluck('name')->join(', ') }}</strong>
                @endif
            </p>
        </div>
    @endif

    <!-- Prize Structure Info Section -->
    <div class="sidebar-section mt-6">
        <div class="sidebar-header">Cơ cấu giải thưởng XSMB</div>
        <div class="p-4 text-sm">
            <p class="mb-2 text-gray-700">(Xổ số truyền thống, xổ số Thủ Đô hay xổ số Hà Nội)</p>
            <p class="mb-1 font-medium">Loại vé: 10.000 đ</p>
            <p class="mb-3">Có 81.150 giải thưởng (27 số trúng đương với 27 lần quay)</p>

            <table class="w-full border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 py-2 px-2 text-left">Hạng giải</th>
                        <th class="border border-gray-300 py-2 px-2 text-center">Số giải</th>
                        <th class="border border-gray-300 py-2 px-2 text-right">Giá trị</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 py-2 px-2">Giải đặc biệt</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">15</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">200.000.000 đ</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 py-2 px-2">Giải nhất</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">15</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">50.000.000 đ</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 py-2 px-2">Giải nhì</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">30</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">15.000.000 đ</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 py-2 px-2">Giải ba</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">90</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">5.000.000 đ</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 py-2 px-2">Giải tư</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">300</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">1.000.000 đ</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 py-2 px-2">Giải năm</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">900</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">200.000 đ</td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 py-2 px-2">Giải sáu</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">1.800</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">100.000 đ</td>
                    </tr>
                    <tr class="bg-gray-50">
                        <td class="border border-gray-300 py-2 px-2">Giải bảy</td>
                        <td class="border border-gray-300 py-2 px-2 text-center">78.000</td>
                        <td class="border border-gray-300 py-2 px-2 text-right">40.000 đ</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="sidebar-section mt-4">
        <div class="sidebar-header">Câu hỏi thường gặp về XSMB</div>
        <div class="p-4 text-sm space-y-3">
            <details class="pb-3 border-b border-gray-200">
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    Mấy giờ có kết quả xổ số miền Bắc?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Kết quả xổ số miền Bắc được mở thưởng lúc 18h15 hàng ngày, từ thứ Hai đến Chủ Nhật. Kết quả được cập nhật trực tiếp trên trang web ngay sau khi có thông tin chính thức.
                </p>
            </details>

            <details class="pb-3 border-b border-gray-200">
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    XSMB quay vào những ngày nào trong tuần?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Xổ số miền Bắc (Hà Nội) quay hằng ngày từ thứ Hai đến Chủ Nhật. Ngoài ra còn có các tỉnh khác như Quảng Ninh, Bắc Ninh, Hải Phòng, Nam Định, Thái Bình quay vào các ngày cụ thể trong tuần.
                </p>
            </details>

            <details class="pb-3 border-b border-gray-200">
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    Làm sao để tra cứu kết quả XSMB theo ngày?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Bạn có thể sử dụng công cụ chọn ngày ở trên để xem kết quả XSMB theo ngày bất kỳ trong quá khứ. Chỉ cần chọn ngày và nhấn "Xem" để xem kết quả.
                </p>
            </details>

            <details>
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    Bảng loto Đầu/Đuôi là gì?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Bảng loto Đầu/Đuôi giúp phân tích các con số theo chữ số đầu và chữ số cuối. Đây là công cụ hữu ích cho người chơi để theo dõi tần suất xuất hiện của các con số và đưa ra dự đoán cho kỳ quay tiếp theo.
                </p>
            </details>
        </div>
    </div>

    <!-- Draw Schedule Info -->
    <div class="sidebar-section mt-4">
        <div class="sidebar-header">Lịch quay xổ số miền Bắc</div>
        <div class="p-4">
            <div class="grid grid-cols-1 gap-2 text-sm">
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 2:</span>
                    <span class="text-gray-600">Hà Nội</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 3:</span>
                    <span class="text-gray-600">Hà Nội, Quảng Ninh</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 4:</span>
                    <span class="text-gray-600">Hà Nội, Bắc Ninh</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 5:</span>
                    <span class="text-gray-600">Hà Nội</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 6:</span>
                    <span class="text-gray-600">Hà Nội, Hải Phòng</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 7:</span>
                    <span class="text-gray-600">Hà Nội, Nam Định</span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-24">Chủ Nhật:</span>
                    <span class="text-gray-600">Hà Nội, Thái Bình</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('right-sidebar')
    <x-lottery-sidebar
        :northProvinces="$northProvinces ?? collect([])"
        :centralProvinces="$centralProvinces ?? collect([])"
        :southProvinces="$southProvinces ?? collect([])"
        :showCalendar="true"
        :showProvinces="true"
        region="xsmb"
    />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('xsmb') }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const dateInput = this.querySelector('input[name="date"]');
            if (dateInput && dateInput.value) {
                const parts = dateInput.value.split('-'); // Y-m-d
                const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`; // d-m-Y
                window.location.href = `{{ route('xsmb') }}/${formattedDate}`;
            } else {
                window.location.href = `{{ route('xsmb') }}`;
            }
        });
    }
});
</script>
@endsection
