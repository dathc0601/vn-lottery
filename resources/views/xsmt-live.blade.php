@extends('layouts.app')

@section('title', 'XSMT Trực Tiếp - Kết Quả Xổ Số Miền Trung Hôm Nay')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('xsmt') }}" class="text-[#0066cc] hover:underline">XSMT</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Trực tiếp</span>
@endsection

@section('page-content')
<div x-data="liveXsmt()" x-init="init()">
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <!-- Page Header (Orange bar) -->
            <div class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    XSMT Trực Tiếp - Kết Quả Xổ Số Miền Trung Hôm Nay
                </div>
            </div>

            <!-- Status Indicator -->
            <div class="mb-4">
                <template x-if="status === 'before'">
                    <div class="bg-blue-50 border border-blue-200 px-4 py-3 rounded flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm text-blue-800">Phiên quay số bắt đầu lúc <strong>17:15</strong>. Kết quả sẽ được cập nhật tự động.</span>
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
                    KQXS Miền Trung hôm nay <span x-text="todayDateDisplay"></span> hiện <strong class="text-red-600">chưa có</strong>, dưới đây là kết quả XSMT mới nhất (<span x-text="resultsDateDisplay"></span>)
                </div>
            </template>

            <!-- Result Card -->
            <div class="result-card border border-gray-300 bg-white mb-5">
                <!-- Yellow Header -->
                <div class="bg-[#fff8dc] px-4 py-3 border-b border-gray-300">
                    <h2 class="text-lg font-semibold text-center text-gray-800">
                        XSMT Trực Tiếp - <span x-text="todayFormatted"></span>
                    </h2>
                    <div class="text-center text-sm text-[#0066cc] mt-1">
                        <a href="{{ route('xsmt') }}" class="hover:underline">XSMT</a> /
                        <span>Trực tiếp</span>
                    </div>
                </div>

                <!-- Multi-column Prize Table -->
                <div class="overflow-x-auto">
                    <table class="result-table-xsmn-grouped w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 py-2 px-3 text-center w-16">Giải</th>
                                <template x-for="(prov, pi) in provinces" :key="'th-'+pi">
                                    <th class="border border-gray-300 py-2 px-2 text-center min-w-[140px]" x-text="prov.name"></th>
                                </template>
                                <!-- Placeholder columns when no data yet -->
                                <template x-if="provinces.length === 0">
                                    <th class="border border-gray-300 py-2 px-2 text-center min-w-[140px]" colspan="3">
                                        <span class="text-gray-400">Đang tải...</span>
                                    </th>
                                </template>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- G.8 -->
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G8</td>
                                <template x-for="(prov, pi) in provinces" :key="'g8-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_8">
                                            <span class="font-bold number" x-text="prov.prizes.prize_8"></span>
                                        </template>
                                        <template x-if="!prov.prizes.prize_8">
                                            <span x-html="placeholder()"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholder()"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.7 -->
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G7</td>
                                <template x-for="(prov, pi) in provinces" :key="'g7-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_7">
                                            <span class="number" x-text="prov.prizes.prize_7"></span>
                                        </template>
                                        <template x-if="!prov.prizes.prize_7">
                                            <span x-html="placeholder()"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholder()"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.6 (3 numbers) -->
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G6</td>
                                <template x-for="(prov, pi) in provinces" :key="'g6-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_6">
                                            <div class="flex flex-wrap justify-center gap-1">
                                                <template x-for="(num, i) in prov.prizes.prize_6.split(',')" :key="'g6-'+pi+'-'+i">
                                                    <span class="number" x-text="num.trim()"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!prov.prizes.prize_6">
                                            <span x-html="placeholderMulti(3)"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholderMulti(3)"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.5 -->
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G5</td>
                                <template x-for="(prov, pi) in provinces" :key="'g5-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_5">
                                            <span class="number" x-text="prov.prizes.prize_5"></span>
                                        </template>
                                        <template x-if="!prov.prizes.prize_5">
                                            <span x-html="placeholder()"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholder()"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.4 (7 numbers) -->
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G4</td>
                                <template x-for="(prov, pi) in provinces" :key="'g4-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_4">
                                            <div class="flex flex-wrap justify-center gap-1">
                                                <template x-for="(num, i) in prov.prizes.prize_4.split(',')" :key="'g4-'+pi+'-'+i">
                                                    <span class="number" x-text="num.trim()"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!prov.prizes.prize_4">
                                            <span x-html="placeholderMulti(7)"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholderMulti(7)"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.3 (2 numbers) -->
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G3</td>
                                <template x-for="(prov, pi) in provinces" :key="'g3-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_3">
                                            <div class="flex flex-wrap justify-center gap-1">
                                                <template x-for="(num, i) in prov.prizes.prize_3.split(',')" :key="'g3-'+pi+'-'+i">
                                                    <span class="number" x-text="num.trim()"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!prov.prizes.prize_3">
                                            <span x-html="placeholderMulti(2)"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholderMulti(2)"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.2 -->
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G2</td>
                                <template x-for="(prov, pi) in provinces" :key="'g2-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_2">
                                            <span class="number" x-text="prov.prizes.prize_2"></span>
                                        </template>
                                        <template x-if="!prov.prizes.prize_2">
                                            <span x-html="placeholder()"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholder()"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- G.1 -->
                            <tr class="bg-gray-50">
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-gray-100">G1</td>
                                <template x-for="(prov, pi) in provinces" :key="'g1-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_1">
                                            <span class="number" x-text="prov.prizes.prize_1"></span>
                                        </template>
                                        <template x-if="!prov.prizes.prize_1">
                                            <span x-html="placeholder()"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholder()"></span>
                                    </td>
                                </template>
                            </tr>

                            <!-- DB - Special Prize -->
                            <tr>
                                <td class="border border-gray-300 py-2 px-3 text-center font-semibold bg-[#fff8dc]">ĐB</td>
                                <template x-for="(prov, pi) in provinces" :key="'db-'+pi">
                                    <td class="border border-gray-300 py-2 px-2 text-center">
                                        <template x-if="prov.prizes.prize_special">
                                            <span class="font-bold text-lg text-red-600 number" x-text="prov.prizes.prize_special"></span>
                                        </template>
                                        <template x-if="!prov.prizes.prize_special">
                                            <span x-html="placeholder()"></span>
                                        </template>
                                    </td>
                                </template>
                                <template x-if="provinces.length === 0">
                                    <td class="border border-gray-300 py-2 px-2 text-center" colspan="3">
                                        <span x-html="placeholder()"></span>
                                    </td>
                                </template>
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
                <div class="sidebar-header">Thông tin trực tiếp XSMT</div>
                <div class="p-4 text-sm space-y-2 text-gray-700">
                    <p>Kết quả xổ số miền Trung được mở thưởng trực tiếp lúc <strong>17:15</strong> hàng ngày.</p>
                    <p>Mỗi ngày có 2-3 tỉnh quay số đồng thời, kết quả được cập nhật tự động. Bạn không cần tải lại trang.</p>
                    <p>Thứ tự quay: G8 &rarr; G7 &rarr; G6 &rarr; G5 &rarr; G4 &rarr; G3 &rarr; G2 &rarr; G1 &rarr; ĐB</p>
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
            region="xsmt"
        />
    </div>
</div>
@endsection

@section('scripts')
<script>
function liveXsmt() {
    return {
        status: '{{ $sessionState }}',
        provinces: [],
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
                return '<span class="text-gray-400">---</span>';
            }
            return '<span class="inline-block"><svg class="animate-spin h-5 w-5 text-gray-400 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';
        },

        placeholderMulti(count) {
            let html = '';
            for (let i = 0; i < count; i++) {
                if (this.status === 'before') {
                    html += '<span class="text-gray-400 mx-1">---</span>';
                } else {
                    html += '<span class="inline-block mx-1"><svg class="animate-spin h-4 w-4 text-gray-400 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></span>';
                }
            }
            return html;
        },

        async fetchResults() {
            try {
                const response = await fetch('{{ route("api.xsmt.live") }}');
                const data = await response.json();

                if (data.provinces && data.provinces.length > 0) {
                    this.provinces = data.provinces;
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
                console.error('Failed to fetch live XSMT results:', e);
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

            // 17:15 = 1035, 17:50 = 1070
            const liveStart = 17 * 60 + 15;
            const liveEnd = 17 * 60 + 50;

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
                    delay = msUntilStart + 1000;
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
