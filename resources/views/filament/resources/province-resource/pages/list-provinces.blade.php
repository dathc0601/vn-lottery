<x-filament-panels::page>
    @vite('resources/js/filament/provinces.js')

    <div class="provinces-page">
        {{-- Quick Filters Bar --}}
        <div class="filters-container mb-6">
            {{-- Region Filter Pills --}}
            <div class="filter-pills" x-data="{ activeFilter: @entangle('activeFilter') }">
                <button
                    class="filter-pill"
                    :class="{ 'active': activeFilter === 'all' }"
                    @click="activeFilter = 'all'"
                    wire:click="filterByRegion(null)"
                >
                    Tất cả <span class="count">{{ $this->totalCount }}</span>
                </button>
                <button
                    class="filter-pill"
                    data-region-color="north"
                    :class="{ 'active': activeFilter === 'north' }"
                    @click="activeFilter = 'north'"
                    wire:click="filterByRegion('north')"
                >
                    <span class="pill-dot"></span>
                    Miền Bắc <span class="count">{{ $this->northCount }}</span>
                </button>
                <button
                    class="filter-pill"
                    data-region-color="central"
                    :class="{ 'active': activeFilter === 'central' }"
                    @click="activeFilter = 'central'"
                    wire:click="filterByRegion('central')"
                >
                    <span class="pill-dot"></span>
                    Miền Trung <span class="count">{{ $this->centralCount }}</span>
                </button>
                <button
                    class="filter-pill"
                    data-region-color="south"
                    :class="{ 'active': activeFilter === 'south' }"
                    @click="activeFilter = 'south'"
                    wire:click="filterByRegion('south')"
                >
                    <span class="pill-dot"></span>
                    Miền Nam <span class="count">{{ $this->southCount }}</span>
                </button>
            </div>

            {{-- Search & Sort --}}
            <div class="filters-right">
                <div class="search-box">
                    <svg class="icon-search" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                    <input
                        type="text"
                        placeholder="Tìm kiếm tỉnh thành..."
                        wire:model.live.debounce.300ms="search"
                    >
                </div>
                <select class="sort-select" wire:model.live="sortBy">
                    <option value="sort_order">Thứ tự hiển thị</option>
                    <option value="name">Tên (A-Z)</option>
                    <option value="region">Vùng miền</option>
                    <option value="updated_at">Cập nhật gần đây</option>
                </select>
            </div>
        </div>

        {{-- Province Cards Grid --}}
        @if($this->provinces->count() > 0)
            <div class="provinces-grid" id="provinces-grid">
                @foreach($this->provinces as $province)
                    <div
                        class="province-card"
                        data-region="{{ $province->region }}"
                        data-province-id="{{ $province->id }}"
                        wire:key="province-{{ $province->id }}"
                    >
                        {{-- Regional Accent Bar --}}
                        <div class="region-bar"></div>

                        {{-- Card Header --}}
                        <div class="card-header">
                            <div class="header-left">
                                <h3 class="province-name">{{ $province->name }}</h3>
                                <div class="province-code">{{ $province->code }}</div>
                            </div>
                            <div class="header-right">
                                {{-- Fetch Button with Loading State --}}
                                <button
                                    class="btn-fetch"
                                    x-data="{ loading: false }"
                                    @click="
                                        loading = true;
                                        $wire.fetchProvinceResults({{ $province->id }})
                                            .then(() => loading = false)
                                            .catch(() => loading = false);
                                    "
                                    :class="{ 'loading': loading }"
                                    :disabled="loading"
                                    title="Lấy dữ liệu mới"
                                >
                                    <svg class="icon-refresh" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0118.8-4.3M22 12.5a10 10 0 01-18.8 4.2"/>
                                    </svg>
                                </button>

                                {{-- Settings Quick Access --}}
                                <a
                                    href="{{ route('filament.admin.resources.provinces.edit', $province) }}"
                                    class="btn-settings"
                                    title="Chỉnh sửa"
                                >
                                    <svg class="icon-settings" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12.22 2h-.44a2 2 0 00-2 2v.18a2 2 0 01-1 1.73l-.43.25a2 2 0 01-2 0l-.15-.08a2 2 0 00-2.73.73l-.22.38a2 2 0 00.73 2.73l.15.1a2 2 0 011 1.72v.51a2 2 0 01-1 1.74l-.15.09a2 2 0 00-.73 2.73l.22.38a2 2 0 002.73.73l.15-.08a2 2 0 012 0l.43.25a2 2 0 011 1.73V20a2 2 0 002 2h.44a2 2 0 002-2v-.18a2 2 0 011-1.73l.43-.25a2 2 0 012 0l.15.08a2 2 0 002.73-.73l.22-.39a2 2 0 00-.73-2.73l-.15-.08a2 2 0 01-1-1.74v-.5a2 2 0 011-1.74l.15-.09a2 2 0 00.73-2.73l-.22-.38a2 2 0 00-2.73-.73l-.15.08a2 2 0 01-2 0l-.43-.25a2 2 0 01-1-1.73V4a2 2 0 00-2-2z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>

                                {{-- Active/Inactive Toggle --}}
                                <div
                                    class="toggle-active"
                                    x-data="{ active: {{ $province->is_active ? 'true' : 'false' }} }"
                                    @click="
                                        active = !active;
                                        $wire.toggleActive({{ $province->id }}, active);
                                    "
                                    :title="active ? 'Hoạt động' : 'Tạm ngưng'"
                                >
                                    <span class="status-dot" :class="{ 'inactive': !active }"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Region Badge --}}
                        <div class="region-badge">
                            <svg class="icon-map-pin" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span>{{ \App\Helpers\LotteryHelper::getRegionLabel($province->region) }}</span>
                        </div>

                        {{-- Draw Schedule Section --}}
                        <div class="schedule-section">
                            <div class="section-label">
                                <svg class="icon-clock" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                Lịch Quay Số
                            </div>
                            <div class="schedule-text">
                                {{ \App\Helpers\LotteryHelper::formatDrawSchedule($province->draw_days, $province->draw_time) }}
                            </div>
                        </div>

                        {{-- Data Metrics Section --}}
                        <div class="metrics-section">
                            <div class="section-label">
                                <svg class="icon-chart" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="12" y1="20" x2="12" y2="10"/>
                                    <line x1="18" y1="20" x2="18" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="16"/>
                                </svg>
                                Dữ liệu
                            </div>
                            <div class="metrics-grid">
                                <div class="metric">
                                    <span class="metric-value">{{ $province->total_results }}</span>
                                    <span class="metric-label">kết quả</span>
                                </div>
                                <div class="metric">
                                    <span class="metric-value">{{ $province->last_update_human }}</span>
                                    <span class="metric-label">cập nhật</span>
                                </div>
                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="card-divider"></div>

                        {{-- Footer Actions --}}
                        <div class="card-footer">
                            <a
                                href="{{ route('filament.admin.resources.provinces.edit', $province) }}"
                                class="btn-edit"
                            >
                                <svg class="icon-edit" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                                Chỉnh sửa
                            </a>
                            <div class="sort-order">
                                Sắp xếp: <span class="order-number">#{{ $province->sort_order }}</span>
                            </div>
                            <button class="btn-drag" aria-label="Kéo để sắp xếp lại">
                                <svg class="icon-drag" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"/>
                                    <line x1="8" y1="12" x2="21" y2="12"/>
                                    <line x1="8" y1="18" x2="21" y2="18"/>
                                    <line x1="3" y1="6" x2="3.01" y2="6"/>
                                    <line x1="3" y1="12" x2="3.01" y2="12"/>
                                    <line x1="3" y1="18" x2="3.01" y2="18"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="empty-state">
                <svg class="empty-icon" width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                <h3 class="empty-title">Không tìm thấy tỉnh thành</h3>
                <p class="empty-description">
                    Không có tỉnh thành nào phù hợp với bộ lọc của bạn.
                </p>
                <button class="btn-reset-filters" wire:click="resetFilters">
                    Xóa bộ lọc
                </button>
            </div>
        @endif
    </div>
</x-filament-panels::page>
