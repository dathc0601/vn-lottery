@extends('layouts.app')

@section('title', 'Power 6/55 - Kết Quả Xổ Số Vietlott')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('vietlott.mega645') }}" class="text-[#0066cc] hover:underline">Vietlott</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Power 6/55</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <!-- Page Header (Orange bar) -->
            <div class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    Kết Quả Xổ Số Vietlott - Power 6/55
                </div>
            </div>

            <!-- Tab Navigation -->
            <x-vietlott.tab-navigation activeGame="power655" />

            <!-- Game Info Box -->
            <div class="vietlott-info-box mb-4">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <span class="font-semibold">{{ $gameInfo['name'] }}</span>
                        <span class="text-gray-600 text-sm ml-2">- {{ $gameInfo['description'] }}</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        <span>{{ $gameInfo['schedule'] }}</span>
                        <span class="mx-2">|</span>
                        <span>Quay lúc: {{ $gameInfo['draw_time'] }}</span>
                    </div>
                </div>
            </div>

            <!-- Results Display -->
            @if($results->count() > 0)
                <div id="results-container">
                    @foreach($results as $result)
                        <x-vietlott.power-result-card :result="$result" />
                    @endforeach
                </div>

                <!-- Load More Button -->
                @if($hasMoreResults)
                    <div class="text-center mt-6 mb-4" id="load-more-container">
                        <button
                            id="load-more-btn"
                            data-game-type="power655"
                            data-next-page="1"
                            class="bg-[#ff6600] text-white px-8 py-3 rounded hover:bg-[#ff7700] transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-5 h-5 inline-block mr-2 load-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg class="w-5 h-5 inline-block mr-2 loading-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="load-more-text">Xem thêm kết quả</span>
                        </button>
                    </div>
                @endif
            @else
                <div class="border border-yellow-400 bg-yellow-50 px-4 py-3 rounded mb-4">
                    <p class="font-semibold text-yellow-800">Chưa có kết quả</p>
                    <p class="text-sm text-yellow-700 mt-1">
                        Dữ liệu Power 6/55 sẽ được cập nhật sau mỗi kỳ quay.
                    </p>
                </div>
            @endif

            <!-- Game Info Section -->
            <div class="sidebar-section mt-6">
                <div class="sidebar-header">Về Power 6/55</div>
                <div class="p-4 text-sm text-gray-700">
                    <p class="mb-3"><strong>Power 6/55</strong> là sản phẩm xổ số điện toán với jackpot cao nhất của Vietlott, khởi điểm 30 tỷ đồng.</p>
                    <ul class="list-disc list-inside space-y-1 mb-3">
                        <li>Chọn 6 số từ 01 đến 55</li>
                        <li>Quay số vào Thứ 3, Thứ 5 và Thứ 7</li>
                        <li>Thời gian quay: 18:00</li>
                        <li>Giá vé: {{ $gameInfo['ticket_price'] }}đ</li>
                    </ul>
                    <p><strong>Cơ cấu giải thưởng:</strong></p>
                    <ul class="list-disc list-inside space-y-1 mt-2">
                        <li>Jackpot 1: Trúng 6 số - Từ 30 tỷ đồng</li>
                        <li>Jackpot 2: Trúng 6 số + số đặc biệt - Tích lũy</li>
                        <li>Giải Nhất: Trúng 5 số - 40.000.000đ</li>
                        <li>Giải Nhì: Trúng 4 số - 500.000đ</li>
                        <li>Giải Ba: Trúng 3 số - 50.000đ</li>
                    </ul>
                </div>
            </div>

        </div>

        <!-- Sidebar Column -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
            :showCalendar="false"
            region="vietlott"
        />

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    if (!loadMoreBtn) return;

    loadMoreBtn.addEventListener('click', async function() {
        const gameType = this.dataset.gameType;
        const page = parseInt(this.dataset.nextPage);

        this.disabled = true;
        this.querySelector('.load-icon').classList.add('hidden');
        this.querySelector('.loading-spinner').classList.remove('hidden');
        this.querySelector('.load-more-text').textContent = 'Đang tải...';

        try {
            const response = await fetch('/api/vietlott/load-more/' + gameType + '/' + page);
            const data = await response.json();

            const container = document.getElementById('results-container');
            container.insertAdjacentHTML('beforeend', data.html);

            if (data.hasMore) {
                this.dataset.nextPage = data.nextPage;
                this.disabled = false;
                this.querySelector('.load-icon').classList.remove('hidden');
                this.querySelector('.loading-spinner').classList.add('hidden');
                this.querySelector('.load-more-text').textContent = 'Xem thêm kết quả';
            } else {
                document.getElementById('load-more-container').textContent = 'Đã hiển thị tất cả kết quả';
            }
        } catch (error) {
            console.error('Error loading more results:', error);
            this.disabled = false;
            this.querySelector('.load-icon').classList.remove('hidden');
            this.querySelector('.loading-spinner').classList.add('hidden');
            this.querySelector('.load-more-text').textContent = 'Thử lại';
        }
    });
});
</script>
@endpush
@endsection
