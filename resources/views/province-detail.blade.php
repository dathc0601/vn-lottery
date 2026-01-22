@extends('layouts.app')

@section('title', $province->name . ' - Kết quả xổ số')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    @if($province->region == 'north')
        <a href="{{ route('xsmb') }}" class="text-[#0066cc] hover:underline">XSMB</a>
    @elseif($province->region == 'central')
        <a href="{{ route('xsmt') }}" class="text-[#0066cc] hover:underline">XSMT</a>
    @else
        <a href="{{ route('xsmn') }}" class="text-[#0066cc] hover:underline">XSMN</a>
    @endif
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">{{ $province->name }}</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content -->
        <div class="flex-1 min-w-0">

            <!-- Page Header (Orange bar) -->
            <div class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    @if($province->region == 'north')
                        XSMB - Kết quả xổ số {{ $province->name }} - Xổ Số Miền Bắc
                    @elseif($province->region == 'central')
                        XSMT - Kết quả xổ số {{ $province->name }} - Xổ Số Miền Trung
                    @else
                        XSMN - Kết quả xổ số {{ $province->name }} - Xổ Số Miền Nam
                    @endif
                </div>
            </div>

            <!-- Draw Days Info -->
            @if($province->draw_days && count($province->draw_days) > 0)
            <div class="bg-blue-50 border border-blue-200 p-3 mb-4 rounded">
                <p class="text-sm text-blue-800">
                    @php
                        $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
                        $drawDays = array_map(fn($d) => $days[$d], $province->draw_days);
                    @endphp
                    <strong>Lịch quay số:</strong> {{ implode(', ', $drawDays) }} - Mở thưởng lúc <strong>{{ \Carbon\Carbon::parse($province->draw_time)->format('H:i') }}</strong>
                </p>
            </div>
            @endif

            <!-- Results Display (using existing component) -->
            @if($results->count() > 0)
                <div id="province-results-container">
                    @foreach($results as $result)
                        <x-result-card-xskt :result="$result" :region="$region" />
                    @endforeach
                </div>

                <!-- Load More Button -->
                @if($results->hasMorePages())
                <div class="text-center mt-6 mb-4" id="load-more-province-container">
                    <button
                        id="load-more-province-btn"
                        data-region="{{ $region }}"
                        data-slug="{{ $province->slug }}"
                        data-next-page="2"
                        class="bg-[#ff6600] text-white px-8 py-3 rounded hover:bg-[#ff7700] transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-5 h-5 inline-block mr-2 load-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <svg class="w-5 h-5 inline-block mr-2 loading-spinner hidden animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="load-more-province-text">Xem thêm kết quả XS{{ strtoupper(substr($province->name, 0, 2)) }}</span>
                    </button>
                </div>
                @endif
            @else
                <div class="border border-yellow-400 bg-yellow-50 px-4 py-3 rounded mb-4">
                    <p class="font-semibold text-yellow-800">Chưa có kết quả</p>
                    <p class="text-sm text-yellow-700 mt-1">
                        Không tìm thấy kết quả xổ số cho {{ $province->name }}.
                    </p>
                </div>
            @endif

            <!-- Province Information Section -->
            <div class="bg-white rounded shadow overflow-hidden mt-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    Những thông tin chính về {{ $province->name }}
                </div>
                <div class="p-4 text-sm text-gray-700 space-y-3">
                    @if($province->region == 'north')
                        <p>Xổ số {{ $province->name }} là một trong những đài xổ số thuộc khu vực miền Bắc Việt Nam. Kết quả xổ số được mở thưởng và công bố trực tiếp trên các phương tiện truyền thông chính thức.</p>
                        <p>Xổ số miền Bắc có cơ cấu giải thưởng đặc biệt với 27 lần quay số, mang đến cơ hội trúng thưởng cao cho người chơi. Giải đặc biệt có giá trị lên đến 200 triệu đồng.</p>
                    @elseif($province->region == 'central')
                        <p>Xổ số {{ $province->name }} là một trong những đài xổ số thuộc khu vực miền Trung Việt Nam. Kết quả xổ số được mở thưởng và công bố trực tiếp vào các ngày quay số theo lịch cố định.</p>
                        <p>Xổ số miền Trung có cơ cấu giải thưởng hấp dẫn với giải đặc biệt lên đến 2 tỷ đồng. Kết quả được công bố lúc 17h15 hàng ngày.</p>
                    @else
                        <p>Xổ số {{ $province->name }} là một trong những đài xổ số thuộc khu vực miền Nam Việt Nam. Kết quả xổ số được mở thưởng và công bố trực tiếp vào các ngày quay số theo lịch cố định.</p>
                        <p>Xổ số miền Nam có cơ cấu giải thưởng hấp dẫn với giải đặc biệt lên đến 2 tỷ đồng. Kết quả được công bố lúc 16h15 hàng ngày.</p>
                    @endif
                    <p>Trang web cập nhật kết quả xổ số {{ $province->name }} nhanh chóng, chính xác ngay sau khi có kết quả chính thức từ đài. Người chơi có thể tra cứu kết quả, dò vé số và xem thống kê các kỳ quay trước đây một cách dễ dàng.</p>
                </div>
            </div>

            <!-- Prize Structure Table -->
            <div class="bg-white rounded shadow overflow-hidden mt-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    Cơ cấu giải thưởng xổ số {{ $province->name }}
                </div>
                <div class="p-4">
                    @if($province->region == 'north')
                        @include('partials.prize-structure-xsmb')
                    @else
                        @include('partials.prize-structure-xsmn')
                    @endif
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white rounded shadow overflow-hidden mt-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    Câu hỏi thường gặp
                </div>
                <div class="p-4 text-sm space-y-3">
                    <details class="pb-3 border-b border-gray-200">
                        <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                            Xổ số {{ $province->name }} mở thưởng ngày nào?
                        </summary>
                        <p class="mt-2 text-gray-700 pl-4">
                            @if($province->draw_days && count($province->draw_days) > 0)
                                @php
                                    $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
                                    $drawDays = array_map(fn($d) => $days[$d], $province->draw_days);
                                @endphp
                                Xổ số {{ $province->name }} mở thưởng vào các ngày: {{ implode(', ', $drawDays) }} hàng tuần.
                            @else
                                Vui lòng liên hệ đài xổ số để biết lịch quay số cụ thể.
                            @endif
                        </p>
                    </details>

                    <details class="pb-3 border-b border-gray-200">
                        <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                            Mấy giờ có kết quả xổ số {{ $province->name }}?
                        </summary>
                        <p class="mt-2 text-gray-700 pl-4">
                            Kết quả xổ số {{ $province->name }} được mở thưởng lúc {{ \Carbon\Carbon::parse($province->draw_time)->format('H:i') }} vào các ngày quay số. Kết quả được cập nhật trực tiếp trên trang web ngay sau khi có thông tin chính thức từ đài.
                        </p>
                    </details>

                    <details class="pb-3 border-b border-gray-200">
                        <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                            Làm sao để tra cứu kết quả xổ số {{ $province->name }} theo ngày?
                        </summary>
                        <p class="mt-2 text-gray-700 pl-4">
                            Bạn có thể sử dụng trang này để xem kết quả xổ số {{ $province->name }} theo ngày. Kết quả được sắp xếp theo thứ tự từ mới nhất đến cũ nhất. Bạn cũng có thể sử dụng phân trang ở cuối trang để xem các kết quả cũ hơn.
                        </p>
                    </details>

                    <details>
                        <summary class="cursor-pointer font-medium text-gray-800 hover:text-[#ff6600]">
                            Giải đặc biệt xổ số {{ $province->name }} có giá trị bao nhiêu?
                        </summary>
                        <p class="mt-2 text-gray-700 pl-4">
                            @if($province->region == 'north')
                                Giải đặc biệt xổ số {{ $province->name }} có giá trị 200 triệu đồng cho mỗi giải. Với 15 giải đặc biệt được quay mỗi kỳ, tổng giá trị giải đặc biệt lên đến 3 tỷ đồng.
                            @else
                                Giải đặc biệt xổ số {{ $province->name }} có giá trị 2 tỷ đồng. Đây là giải thưởng cao nhất trong cơ cấu giải thưởng xổ số kiến thiết miền {{ $province->region == 'central' ? 'Trung' : 'Nam' }}.
                            @endif
                        </p>
                    </details>
                </div>
            </div>

            <!-- Draw Schedule Table -->
            <div class="bg-white rounded shadow overflow-hidden mt-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    Lịch quay và phát sóng xổ số {{ $province->name }}
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Schedule Column -->
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Lịch quay số</h4>
                            <table class="w-full border-collapse text-sm">
                                <tbody>
                                    @if($province->draw_days && count($province->draw_days) > 0)
                                        @php
                                            $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'Chủ Nhật'];
                                        @endphp
                                        @foreach($province->draw_days as $day)
                                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                                <td class="border border-gray-300 py-2 px-3 font-medium">{{ $days[$day] }}</td>
                                                <td class="border border-gray-300 py-2 px-3">{{ \Carbon\Carbon::parse($province->draw_time)->format('H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="border border-gray-300 py-2 px-3 text-gray-500" colspan="2">Chưa có thông tin lịch quay</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Broadcast Column -->
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Kênh phát sóng</h4>
                            <table class="w-full border-collapse text-sm">
                                <tbody>
                                    @if($province->region == 'north')
                                        <tr>
                                            <td class="border border-gray-300 py-2 px-3 font-medium">VTV1</td>
                                            <td class="border border-gray-300 py-2 px-3">18:15</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="border border-gray-300 py-2 px-3 font-medium">Đài PTTH Hà Nội</td>
                                            <td class="border border-gray-300 py-2 px-3">18:15</td>
                                        </tr>
                                    @elseif($province->region == 'central')
                                        <tr>
                                            <td class="border border-gray-300 py-2 px-3 font-medium">VTV5</td>
                                            <td class="border border-gray-300 py-2 px-3">17:15</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="border border-gray-300 py-2 px-3 font-medium">Đài PTTH địa phương</td>
                                            <td class="border border-gray-300 py-2 px-3">17:15</td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td class="border border-gray-300 py-2 px-3 font-medium">HTV9</td>
                                            <td class="border border-gray-300 py-2 px-3">16:15</td>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <td class="border border-gray-300 py-2 px-3 font-medium">Đài PTTH địa phương</td>
                                            <td class="border border-gray-300 py-2 px-3">16:15</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Sidebar -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
            :showCalendar="true"
            :showProvinces="true"
            :region="$region"
        />
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-province-btn');
    const resultsContainer = document.getElementById('province-results-container');
    const loadMoreContainer = document.getElementById('load-more-province-container');

    if (!loadMoreBtn || !resultsContainer) {
        return;
    }

    let isLoading = false;

    loadMoreBtn.addEventListener('click', async function() {
        if (isLoading) return;

        const region = this.dataset.region;
        const slug = this.dataset.slug;
        const nextPage = this.dataset.nextPage;

        if (!region || !slug || !nextPage) {
            console.error('Missing region, slug or page');
            return;
        }

        isLoading = true;
        setLoadingState(true);

        try {
            const response = await fetch(`/api/load-more-province/${region}/${slug}/${nextPage}`);

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.error) {
                showError(data.error);
                return;
            }

            if (data.html && data.resultsCount > 0) {
                // Append new results using DOMParser for safer HTML insertion
                const parser = new DOMParser();
                const doc = parser.parseFromString(data.html, 'text/html');
                const newElements = doc.body.children;

                // Move all children from parsed document to results container
                while (newElements.length > 0) {
                    resultsContainer.appendChild(newElements[0]);
                }

                // Re-initialize digit display radios for new cards
                initializeDigitDisplayRadios();

                // Update button with next page
                if (data.hasMore && data.nextPage) {
                    loadMoreBtn.dataset.nextPage = data.nextPage;
                } else {
                    // No more results, show message and disable button
                    showNoMoreResults();
                }
            } else if (!data.hasMore) {
                showNoMoreResults();
            }

        } catch (error) {
            console.error('Error loading more results:', error);
            showError('Có lỗi xảy ra. Vui lòng thử lại.');
        } finally {
            isLoading = false;
            setLoadingState(false);
        }
    });

    function setLoadingState(loading) {
        const loadIcon = loadMoreBtn.querySelector('.load-icon');
        const loadingSpinner = loadMoreBtn.querySelector('.loading-spinner');
        const loadMoreText = document.getElementById('load-more-province-text');

        if (loading) {
            loadMoreBtn.disabled = true;
            if (loadIcon) loadIcon.classList.add('hidden');
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');
            if (loadMoreText) loadMoreText.textContent = 'Đang tải...';
        } else {
            loadMoreBtn.disabled = false;
            if (loadIcon) loadIcon.classList.remove('hidden');
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
            if (loadMoreText) loadMoreText.textContent = 'Xem thêm kết quả XS{{ strtoupper(substr($province->name, 0, 2)) }}';
        }
    }

    function showNoMoreResults() {
        loadMoreBtn.disabled = true;
        loadMoreBtn.classList.remove('bg-[#ff6600]', 'hover:bg-[#ff7700]');
        loadMoreBtn.classList.add('bg-gray-400', 'cursor-not-allowed');

        const loadIcon = loadMoreBtn.querySelector('.load-icon');
        const loadMoreText = document.getElementById('load-more-province-text');

        if (loadIcon) loadIcon.classList.add('hidden');
        if (loadMoreText) loadMoreText.textContent = 'Đã hiển thị tất cả kết quả';
    }

    function showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4 text-center';

        const messagePara = document.createElement('p');
        messagePara.textContent = message;
        errorDiv.appendChild(messagePara);

        const retryBtn = document.createElement('button');
        retryBtn.className = 'mt-2 text-sm underline hover:no-underline retry-btn';
        retryBtn.textContent = 'Thử lại';
        retryBtn.addEventListener('click', function() {
            errorDiv.remove();
            loadMoreBtn.click();
        });
        errorDiv.appendChild(retryBtn);

        loadMoreContainer.insertBefore(errorDiv, loadMoreBtn);
    }

    function initializeDigitDisplayRadios() {
        // Find all result cards and re-initialize their digit display radios
        const resultCards = resultsContainer.querySelectorAll('.result-card');

        resultCards.forEach(card => {
            const cardId = card.id.replace('result-', '');
            const radios = card.querySelectorAll(`input[name="digit-display-${cardId}"]`);

            radios.forEach(radio => {
                // Remove existing listeners by cloning
                const newRadio = radio.cloneNode(true);
                radio.parentNode.replaceChild(newRadio, radio);

                newRadio.addEventListener('change', function() {
                    const displayType = this.value;
                    const numbers = card.querySelectorAll('.result-table-xskt .number');

                    numbers.forEach(numberSpan => {
                        const originalNumber = numberSpan.getAttribute('data-original') || numberSpan.textContent.trim();

                        // Store original if not stored yet
                        if (!numberSpan.getAttribute('data-original')) {
                            numberSpan.setAttribute('data-original', originalNumber);
                        }

                        if (displayType === 'all') {
                            numberSpan.textContent = originalNumber;
                        } else if (displayType === '2') {
                            // Show last 2 digits
                            if (originalNumber.length >= 2) {
                                numberSpan.textContent = originalNumber.slice(-2);
                            }
                        } else if (displayType === '3') {
                            // Show last 3 digits
                            if (originalNumber.length >= 3) {
                                numberSpan.textContent = originalNumber.slice(-3);
                            }
                        }
                    });
                });
            });
        });
    }
});
</script>
@endsection
