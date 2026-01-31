@php
    use App\Models\Prediction;

    $regions = [
        ['slug' => 'xsmb', 'name' => 'Miền Bắc', 'region' => Prediction::REGION_NORTH],
        ['slug' => 'xsmt', 'name' => 'Miền Trung', 'region' => Prediction::REGION_CENTRAL],
        ['slug' => 'xsmn', 'name' => 'Miền Nam', 'region' => Prediction::REGION_SOUTH],
    ];
@endphp

<aside class="w-full lg:w-[280px] flex-shrink-0">
    <div class="sticky top-4 space-y-4">
        {{-- Region Links --}}
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                Dự đoán theo khu vực
            </div>
            <div class="p-4 space-y-2">
                @foreach($regions as $r)
                    <a href="{{ route('prediction.' . $r['slug'] . '.index') }}"
                       class="flex items-center justify-between p-3 rounded-lg border transition-all
                              {{ $regionSlug === $r['slug'] ? 'border-[#ff6600] bg-orange-50 text-[#ff6600]' : 'border-gray-200 hover:border-[#ff6600] hover:bg-gray-50' }}">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 flex items-center justify-center rounded-full
                                         {{ $regionSlug === $r['slug'] ? 'bg-[#ff6600] text-white' : 'bg-gray-100 text-gray-600' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <div>
                                <span class="font-semibold block">{{ strtoupper($r['slug']) }}</span>
                                <span class="text-xs text-gray-500">{{ $r['name'] }}</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Quick Links --}}
        <div class="bg-white rounded shadow overflow-hidden">
            <div class="bg-blue-500 text-white px-4 py-2 font-medium">
                Liên kết nhanh
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('statistics') }}"
                   class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 text-gray-700 hover:text-[#ff6600] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Thống kê xổ số
                </a>
                <a href="{{ route('trial.draw') }}"
                   class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 text-gray-700 hover:text-[#ff6600] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Quay thử xổ số
                </a>
                <a href="{{ route('results.book') }}"
                   class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 text-gray-700 hover:text-[#ff6600] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    Sổ kết quả
                </a>
                <a href="{{ route('schedule') }}"
                   class="flex items-center gap-2 p-2 rounded hover:bg-gray-50 text-gray-700 hover:text-[#ff6600] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Lịch mở thưởng
                </a>
            </div>
        </div>

        {{-- Info Box --}}
        <div class="bg-gradient-to-br from-orange-50 to-yellow-50 rounded shadow p-4 border border-orange-200">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0">
                    <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 mb-1">Lưu ý</h4>
                    <p class="text-sm text-gray-600">
                        Các dự đoán trên trang này chỉ mang tính chất tham khảo và giải trí.
                        Kết quả xổ số hoàn toàn ngẫu nhiên.
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>
