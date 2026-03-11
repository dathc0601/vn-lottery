@extends('layouts.app')

@section('title', 'XSMB Trực Tiếp - Kết Quả Xổ Số Miền Bắc Hôm Nay')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('xsmb') }}" class="text-[#0066cc] hover:underline">XSMB</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Trực tiếp</span>
@endsection

@section('page-content')
<div x-data="liveXsmb()" x-init="init()">
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <!-- Page Header (Orange bar) -->
            <div class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="bg-[#ff6600] text-white px-4 py-2">
                    <h1 class="text-lg font-bold">XSMB Trực Tiếp - Kết Quả Xổ Số Miền Bắc Hôm Nay</h1>
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="mb-4">
                <template x-if="status === 'before'">
                    <div class="bg-blue-50 border border-blue-200 px-4 py-3 rounded flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-blue-800">Phiên quay số bắt đầu lúc <strong>18:15</strong>. Kết quả sẽ được cập nhật tự động.</span>
                    </div>
                </template>
                <template x-if="status === 'live' || status === 'in_progress'">
                    <div class="bg-red-50 border border-red-200 px-4 py-3 rounded flex items-center">
                        <span class="relative flex h-3 w-3 mr-2 flex-shrink-0">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                        </span>
                        <span class="text-sm text-red-800 font-medium">Đang trực tiếp - Kết quả đang được cập nhật</span>
                    </div>
                </template>
            </div>

            <!-- Previous Day Note -->
            <template x-if="isPreviousDay && todayDateDisplay && resultsDateDisplay">
                <div class="bg-orange-50 border border-orange-300 px-4 py-3 rounded mb-4 text-sm text-gray-800">
                    KQXS Miền Bắc hôm nay <span x-text="todayDateDisplay"></span> hiện <strong class="text-red-600">chưa có</strong>, dưới đây là kết quả XSMB mới nhất (<span x-text="resultsDateDisplay"></span>)
                </div>
            </template>

            <!-- Result Card -->
            <div class="result-card border border-gray-300 bg-white mb-5">
                <!-- Yellow Header -->
                <div class="bg-[#fff8dc] px-4 py-3 border-b border-gray-300">
                    <h2 class="text-lg font-semibold text-center text-gray-800">
                        XSMB Trực Tiếp - <span x-text="todayFormatted"></span>
                    </h2>
                    <div class="text-center text-sm text-[#0066cc] mt-1">
                        <a href="{{ route('xsmb') }}" class="hover:underline">XSMB</a> /
                        <span>Trực tiếp</span>
                    </div>
                </div>

                <!-- Prize Table -->
                <div class="p-4">
                    <table class="result-table-xskt w-full">
                        <tbody>
                            <!-- ĐB - Special Prize -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center w-12 bg-[#fff8dc]">ĐB</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_special">
                                        <span class="font-bold text-2xl text-red-600 number" x-text="prizes.prize_special"></span>
                                    </template>
                                    <template x-if="!prizes.prize_special">
                                        <span x-html="placeholder()"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G1 -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center bg-gray-50">G1</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_1">
                                        <span class="font-semibold text-lg number" x-text="prizes.prize_1"></span>
                                    </template>
                                    <template x-if="!prizes.prize_1">
                                        <span x-html="placeholder()"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G2 (2 numbers) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center bg-gray-50">G2</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_2">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_2.split(',')" :key="'g2-'+i">
                                                <span class="font-medium number mx-3" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_2">
                                        <span x-html="placeholderMulti(2)"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G3 row 1 (first 3 of 6) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center bg-gray-50" rowspan="2">G3</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_3">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_3.split(',').slice(0, 3)" :key="'g3a-'+i">
                                                <span class="number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_3">
                                        <span x-html="placeholderMulti(3)"></span>
                                    </template>
                                </td>
                            </tr>
                            <!-- G3 row 2 (last 3 of 6) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_3 && prizes.prize_3.split(',').length > 3">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_3.split(',').slice(3)" :key="'g3b-'+i">
                                                <span class="number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_3">
                                        <span x-html="placeholderMulti(3)"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G4 (4 numbers) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center bg-gray-50">G4</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_4">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_4.split(',')" :key="'g4-'+i">
                                                <span class="number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_4">
                                        <span x-html="placeholderMulti(4)"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G5 row 1 (first 3 of 6) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center bg-gray-50" rowspan="2">G5</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_5">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_5.split(',').slice(0, 3)" :key="'g5a-'+i">
                                                <span class="number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_5">
                                        <span x-html="placeholderMulti(3)"></span>
                                    </template>
                                </td>
                            </tr>
                            <!-- G5 row 2 (last 3 of 6) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_5 && prizes.prize_5.split(',').length > 3">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_5.split(',').slice(3)" :key="'g5b-'+i">
                                                <span class="number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_5">
                                        <span x-html="placeholderMulti(3)"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G6 (3 numbers) -->
                            <tr class="border-b border-gray-200">
                                <td class="py-2 font-semibold text-center bg-gray-50">G6</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_6">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_6.split(',')" :key="'g6-'+i">
                                                <span class="number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_6">
                                        <span x-html="placeholderMulti(3)"></span>
                                    </template>
                                </td>
                            </tr>

                            <!-- G7 (4 numbers) -->
                            <tr>
                                <td class="py-2 font-semibold text-center bg-gray-50">G7</td>
                                <td class="py-2 text-center">
                                    <template x-if="prizes.prize_7">
                                        <span>
                                            <template x-for="(num, i) in prizes.prize_7.split(',')" :key="'g7-'+i">
                                                <span class="font-bold text-red-600 number mx-2" x-text="num.trim()"></span>
                                            </template>
                                        </span>
                                    </template>
                                    <template x-if="!prizes.prize_7">
                                        <span x-html="placeholderMulti(4)"></span>
                                    </template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Last Updated -->
                <div class="px-4 pb-3 border-t border-gray-200 pt-2 text-sm text-gray-500 text-center">
                    <template x-if="lastUpdated">
                        <span>Cập nhật lúc: <span x-text="lastUpdated"></span></span>
                    </template>
                    <template x-if="!lastUpdated">
                        <span>Đang tải dữ liệu...</span>
                    </template>
                </div>
            </div>

            <!-- Info Section -->
            <div class="sidebar-section mt-4">
                <div class="sidebar-header">Thông tin trực tiếp XSMB</div>
                <div class="p-4 text-sm space-y-2 text-gray-700">
                    <p>Kết quả xổ số miền Bắc được mở thưởng trực tiếp lúc <strong>18:15</strong> hàng ngày.</p>
                    <p>Trang này tự động cập nhật kết quả khi phiên quay số đang diễn ra. Bạn không cần tải lại trang.</p>
                    <p>Thứ tự quay: G7 &rarr; G6 &rarr; G5 &rarr; G4 &rarr; G3 &rarr; G2 &rarr; G1 &rarr; ĐB</p>
                </div>
            </div>

        </div>

        <!-- Right Sidebar -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces ?? collect([])"
            :centralProvinces="$centralProvinces ?? collect([])"
            :southProvinces="$southProvinces ?? collect([])"
            :showCalendar="true"
            :showProvinces="true"
            region="xsmb"
        />
    </div>
</div>
@endsection

@section('scripts')
<script>
function liveXsmb() {
    return {
        status: '{{ $sessionState }}',
        prizes: {
            prize_special: null,
            prize_1: null,
            prize_2: null,
            prize_3: null,
            prize_4: null,
            prize_5: null,
            prize_6: null,
            prize_7: null,
        },
        lastUpdated: null,
        pollInterval: null,
        todayFormatted: '',
        isPreviousDay: false,
        resultsDateDisplay: null,
        todayDateDisplay: null,

        init() {
            const now = new Date();
            const day = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            this.todayFormatted = day + '/' + month + '/' + year;

            this.fetchResults();
            this.startPolling();
        },

        placeholder() {
            if (this.status === 'before') {
                return '<span class="text-gray-400 text-2xl">---</span>';
            }
            return '<span class="inline-block"><svg class="animate-spin h-5 w-5 text-gray-400 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';
        },

        placeholderMulti(count) {
            let html = '';
            for (let i = 0; i < count; i++) {
                if (this.status === 'before') {
                    html += '<span class="text-gray-400 mx-2">---</span>';
                } else {
                    html += '<span class="inline-block mx-2"><svg class="animate-spin h-5 w-5 text-gray-400 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';
                }
            }
            return html;
        },

        async fetchResults() {
            try {
                const response = await fetch('{{ route("api.xsmb.live") }}');
                const data = await response.json();

                if (data.prizes) {
                    this.prizes = data.prizes;
                }
                if (data.status) {
                    this.status = data.status;
                }
                if (data.timestamp) {
                    const ts = new Date(data.timestamp);
                    this.lastUpdated = String(ts.getHours()).padStart(2, '0') + ':' +
                                       String(ts.getMinutes()).padStart(2, '0') + ':' +
                                       String(ts.getSeconds()).padStart(2, '0');
                }
                this.isPreviousDay = !!data.is_previous_day;
                this.resultsDateDisplay = data.results_date_display || null;
                this.todayDateDisplay = data.today_date_display || null;
            } catch (e) {
                console.error('Failed to fetch live results:', e);
            }
        },

        startPolling() {
            this.scheduleNextPoll();
        },

        scheduleNextPoll() {
            if (this.pollInterval) {
                clearTimeout(this.pollInterval);
            }

            const now = new Date();
            const hours = now.getHours();
            const minutes = now.getMinutes();
            const currentMinutes = hours * 60 + minutes;

            // 18:15 = 1095, 18:50 = 1130
            const liveStart = 18 * 60 + 15;
            const liveEnd = 18 * 60 + 50;

            let delay;
            if (currentMinutes >= liveStart && currentMinutes <= liveEnd) {
                // During live window: poll every 30 seconds
                delay = 30 * 1000;
                if (this.status === 'before') {
                    this.status = 'live';
                }
            } else if (this.status === 'complete') {
                // After completion: poll every 5 minutes
                delay = 5 * 60 * 1000;
            } else if (currentMinutes < liveStart) {
                // Before live: check if we're close (within 1 min), otherwise 60s
                const msUntilStart = (liveStart - currentMinutes) * 60 * 1000;
                if (msUntilStart <= 60000) {
                    delay = msUntilStart + 1000; // Wait until just after 18:15
                } else {
                    delay = 60 * 1000;
                }
            } else {
                // After live window: poll every 5 minutes
                delay = 5 * 60 * 1000;
            }

            this.pollInterval = setTimeout(() => {
                this.fetchResults().then(() => {
                    this.scheduleNextPoll();
                });
            }, delay);
        },
    };
}
</script>
@endsection
