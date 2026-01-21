@extends('layouts.app-three-column')

@section('title', 'XSMN - Kết Quả Xổ Số Miền Nam - SXMN Hôm Nay')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">XSMN</span>
@endsection

@section('left-sidebar')
    <x-left-sidebar />
@endsection

@section('page-content')
    <!-- Date Selector -->
    <div class="sidebar-section mb-4">
        <div class="sidebar-header">Chọn ngày xem kết quả</div>
        <div class="p-3">
            <form method="GET" action="{{ route('xsmn') }}" class="flex items-center gap-2">
                <input type="date"
                       name="date"
                       value="{{ $date->format('Y-m-d') }}"
                       max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}"
                       class="flex-1 px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600]">
                <button type="submit"
                        class="px-4 py-2 bg-[#ff6600] text-white rounded hover:bg-[#ff7700] transition-colors font-medium text-sm">
                    Xem
                </button>
                <a href="{{ route('xsmn') }}"
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
        <!-- Results Container -->
        <div id="results-container">
            @foreach($results as $result)
                <x-result-card-xskt :result="$result" region="xsmn" />
            @endforeach
        </div>

        <!-- Load More Button -->
        <div class="text-center mt-6 mb-4" id="load-more-container">
            <button
                id="load-more-btn"
                data-region="xsmn"
                data-next-date="{{ $date->copy()->subDay()->format('d-m-Y') }}"
                class="bg-[#ff6600] text-white px-8 py-3 rounded hover:bg-[#ff7700] transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5 inline-block mr-2 load-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <svg class="w-5 h-5 inline-block mr-2 loading-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="load-more-text">Xem thêm KQXS MN</span>
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

    <!-- Draw Schedule Info -->
    <div class="sidebar-section mt-6">
        <div class="sidebar-header">Lịch quay xổ số miền Nam</div>
        <div class="p-4">
            <div class="grid grid-cols-1 gap-2 text-sm">
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 2:</span>
                    <span class="text-gray-600">TP.HCM, Đồng Tháp, Cà Mau</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 3:</span>
                    <span class="text-gray-600">Bến Tre, Vũng Tàu, Bạc Liêu</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 4:</span>
                    <span class="text-gray-600">Đồng Nai, Cần Thơ, Sóc Trăng</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 5:</span>
                    <span class="text-gray-600">An Giang, Bình Thuận, Tây Ninh</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 6:</span>
                    <span class="text-gray-600">Vĩnh Long, Bình Dương, Trà Vinh</span>
                </div>
                <div class="flex items-start border-b border-gray-100 pb-2">
                    <span class="font-semibold text-gray-700 w-24">Thứ 7:</span>
                    <span class="text-gray-600">TP.HCM, Long An, Bình Phước, Hậu Giang</span>
                </div>
                <div class="flex items-start">
                    <span class="font-semibold text-gray-700 w-24">Chủ Nhật:</span>
                    <span class="text-gray-600">Tiền Giang, Kiên Giang, Đà Lạt</span>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="sidebar-section mt-4">
        <div class="sidebar-header">Câu hỏi thường gặp về XSMN</div>
        <div class="p-4 text-sm space-y-3">
            <details class="pb-3 border-b border-gray-200">
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    Mấy giờ có kết quả xổ số miền Nam?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Kết quả xổ số miền Nam được mở thưởng lúc 16h10 hàng ngày. Mỗi ngày có 3-4 tỉnh quay số độc lập, riêng thứ 7 có 4 tỉnh quay.
                </p>
            </details>

            <details class="pb-3 border-b border-gray-200">
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    XSMN có bao nhiêu tỉnh tham gia?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Hiện tại có 21 tỉnh miền Nam tham gia xổ số kiến thiết, bao gồm TP. Hồ Chí Minh và 20 tỉnh khác như Đồng Tháp, Cà Mau, Bến Tre, Vũng Tàu, Đồng Nai, Cần Thơ, An Giang, Bình Thuận, v.v.
                </p>
            </details>

            <details>
                <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                    Tại sao xổ số miền Nam quay nhiều tỉnh mỗi ngày?
                </summary>
                <p class="mt-2 text-gray-700 pl-4">
                    Khác với miền Bắc chỉ có 1 tỉnh quay mỗi ngày, xổ số miền Nam có nhiều tỉnh quay đồng thời mỗi ngày để phục vụ số lượng người chơi đông đảo hơn trên diện rộng.
                </p>
            </details>
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
        region="xsmn"
    />
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('xsmn') }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const dateInput = this.querySelector('input[name="date"]');
            if (dateInput && dateInput.value) {
                const parts = dateInput.value.split('-'); // Y-m-d
                const formattedDate = `${parts[2]}-${parts[1]}-${parts[0]}`; // d-m-Y
                window.location.href = `{{ route('xsmn') }}/${formattedDate}`;
            } else {
                window.location.href = `{{ route('xsmn') }}`;
            }
        });
    }
});
</script>
@endsection
