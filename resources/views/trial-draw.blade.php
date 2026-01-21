@extends('layouts.app-three-column')

@section('title', 'Quay Thử Xổ Số ' . ($activeTab === 'xsmb' ? 'Miền Bắc' : ($activeTab === 'xsmt' ? 'Miền Trung' : 'Miền Nam')) . ' - Mô Phỏng KQXS')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">Quay thử xổ số</span>
@endsection

@section('left-sidebar')
    <x-left-sidebar />
@endsection

@section('page-content')
    <!-- Tab Navigation -->
    <div class="flex border-b border-gray-300 mb-0">
        <a href="{{ route('trial.xsmb') }}"
           class="px-6 py-3 text-sm font-medium border-t border-l border-r rounded-t -mb-px transition-colors
                  {{ $activeTab === 'xsmb' ? 'bg-white border-gray-300 text-[#0066cc]' : 'bg-gray-100 border-transparent text-gray-600 hover:text-[#0066cc]' }}">
            Quay thử XSMB
        </a>
        <a href="{{ route('trial.xsmt') }}"
           class="px-6 py-3 text-sm font-medium border-t border-l border-r rounded-t -mb-px transition-colors
                  {{ $activeTab === 'xsmt' ? 'bg-white border-gray-300 text-[#0066cc]' : 'bg-gray-100 border-transparent text-gray-600 hover:text-[#0066cc]' }}">
            Quay thử XSMT
        </a>
        <a href="{{ route('trial.xsmn') }}"
           class="px-6 py-3 text-sm font-medium border-t border-l border-r rounded-t -mb-px transition-colors
                  {{ $activeTab === 'xsmn' ? 'bg-white border-gray-300 text-[#0066cc]' : 'bg-gray-100 border-transparent text-gray-600 hover:text-[#0066cc]' }}">
            Quay thử XSMN
        </a>
    </div>

    <!-- Main Content Panel -->
    <div class="bg-white border border-gray-300 border-t-0 p-4">
        <!-- Title -->
        <h1 class="text-xl font-bold text-gray-800 mb-4">
            Quay thử xổ số {{ $regionName }} ngày <span id="currentDate">{{ now()->format('d/m/Y') }}</span>
        </h1>

        @if($activeTab === 'xsmt' || $activeTab === 'xsmn')
            <!-- XSMT/XSMN Day + Province Selector -->
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <label class="text-sm text-gray-700">Chọn ngày:</label>
                <select id="daySelect" class="px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600] min-w-[120px]">
                    <option value="1" {{ ($currentDayOfWeek ?? 1) == 1 ? 'selected' : '' }}>Thứ 2</option>
                    <option value="2" {{ ($currentDayOfWeek ?? 1) == 2 ? 'selected' : '' }}>Thứ 3</option>
                    <option value="3" {{ ($currentDayOfWeek ?? 1) == 3 ? 'selected' : '' }}>Thứ 4</option>
                    <option value="4" {{ ($currentDayOfWeek ?? 1) == 4 ? 'selected' : '' }}>Thứ 5</option>
                    <option value="5" {{ ($currentDayOfWeek ?? 1) == 5 ? 'selected' : '' }}>Thứ 6</option>
                    <option value="6" {{ ($currentDayOfWeek ?? 1) == 6 ? 'selected' : '' }}>Thứ 7</option>
                    <option value="0" {{ ($currentDayOfWeek ?? 1) == 0 ? 'selected' : '' }}>Chủ nhật</option>
                </select>
                <label class="text-sm text-gray-700">Chọn tỉnh:</label>
                <select id="provinceSelect" class="px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600] min-w-[180px]">
                    @foreach($provincesForDay ?? $provinces as $province)
                        <option value="{{ $province->id }}" data-name="{{ $province->name }}" data-slug="{{ $province->slug }}">{{ $province->name }}</option>
                    @endforeach
                </select>
                <button id="startDrawBtn" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#ff7700] transition-colors font-medium text-sm">
                    Bắt đầu quay
                </button>
            </div>

            <!-- Quick Province Tags (XSMT/XSMN style) -->
            <div class="flex flex-wrap items-center gap-2 mb-6">
                <span class="text-sm text-gray-700 font-medium">Quay thử tỉnh:</span>
                @foreach($provinces as $province)
                    <a href="#" class="border border-gray-300 px-2 py-1 rounded text-sm text-gray-700 hover:border-[#ff6600] hover:text-[#ff6600] transition-colors quick-province-link" data-id="{{ $province->id }}" data-name="{{ $province->name }}" data-slug="{{ $province->slug }}">{{ $province->name }}</a>
                @endforeach
            </div>
        @else
            <!-- Province Selector + Button (XSMB) -->
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <label class="text-sm text-gray-700">Chọn tỉnh:</label>
                <select id="provinceSelect" class="px-3 py-2 border border-gray-300 text-sm focus:outline-none focus:border-[#ff6600] min-w-[180px]">
                    @foreach($provinces as $province)
                        <option value="{{ $province->id }}" data-name="{{ $province->name }}">{{ $province->name }}</option>
                    @endforeach
                </select>
                <button id="startDrawBtn" class="px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#ff7700] transition-colors font-medium text-sm">
                    Bắt đầu quay
                </button>
            </div>

            <!-- Quick Province Links (XSMB style) -->
            <div class="text-sm text-gray-600 mb-6">
                <span class="font-medium">Quay thử đài:</span>
                @foreach($provinces->take(6) as $index => $province)
                    <a href="#" class="text-[#0066cc] hover:text-[#ff6600] quick-province-link" data-id="{{ $province->id }}" data-name="{{ $province->name }}">{{ $province->name }}</a>@if(!$loop->last) | @endif
                @endforeach
            </div>
        @endif

        <!-- Prize Table -->
        <div class="mb-6">
            <table class="w-full border-collapse trial-prize-table" id="prizeTable">
                <tbody>
                    @if($activeTab === 'xsmb')
                        <!-- XSMB Prize Structure -->
                        <tr class="bg-[#fff8dc]">
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center w-16 text-red-600">ĐB</td>
                            <td class="border border-gray-300 py-2 px-3 text-center">
                                <span class="prize-number text-2xl font-bold text-red-600" data-prize="db" data-digits="5">
                                    <span class="spinning-placeholder">•••••</span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G1</td>
                            <td class="border border-gray-300 py-2 px-3 text-center">
                                <span class="prize-number text-lg font-bold text-blue-700" data-prize="g1" data-digits="5">
                                    <span class="spinning-placeholder">•••••</span>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G2</td>
                            <td class="border border-gray-300 py-2 px-3 text-center">
                                <span class="prize-number font-semibold" data-prize="g2-0" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                <span class="mx-2">-</span>
                                <span class="prize-number font-semibold" data-prize="g2-1" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G3</td>
                            <td class="border border-gray-300 py-2 px-3 text-center text-sm">
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                    @for($i = 0; $i < 6; $i++)
                                        <span class="prize-number font-medium" data-prize="g3-{{ $i }}" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    @endfor
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G4</td>
                            <td class="border border-gray-300 py-2 px-3 text-center text-sm">
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                    @for($i = 0; $i < 4; $i++)
                                        <span class="prize-number font-medium" data-prize="g4-{{ $i }}" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    @endfor
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G5</td>
                            <td class="border border-gray-300 py-2 px-3 text-center text-sm">
                                <div class="grid grid-cols-2 gap-x-4 gap-y-1">
                                    @for($i = 0; $i < 6; $i++)
                                        <span class="prize-number font-medium" data-prize="g5-{{ $i }}" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    @endfor
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G6</td>
                            <td class="border border-gray-300 py-2 px-3 text-center text-sm">
                                <span class="prize-number font-medium" data-prize="g6-0" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                                <span class="mx-2">-</span>
                                <span class="prize-number font-medium" data-prize="g6-1" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                                <span class="mx-2">-</span>
                                <span class="prize-number font-medium" data-prize="g6-2" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center bg-gray-50">G7</td>
                            <td class="border border-gray-300 py-2 px-3 text-center text-sm">
                                <span class="prize-number font-medium" data-prize="g7-0" data-digits="2"><span class="spinning-placeholder">••</span></span>
                                <span class="mx-2">-</span>
                                <span class="prize-number font-medium" data-prize="g7-1" data-digits="2"><span class="spinning-placeholder">••</span></span>
                                <span class="mx-2">-</span>
                                <span class="prize-number font-medium" data-prize="g7-2" data-digits="2"><span class="spinning-placeholder">••</span></span>
                                <span class="mx-2">-</span>
                                <span class="prize-number font-medium" data-prize="g7-3" data-digits="2"><span class="spinning-placeholder">••</span></span>
                            </td>
                        </tr>
                    @elseif($activeTab === 'xsmt')
                        <!-- XSMT Multi-Province Prize Structure -->
                        <tr class="bg-[#fffde7]">
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center w-14 bg-[#fff8dc]">Tỉnh</td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell" data-province-index="0">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell" data-province-index="1">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell hidden" data-province-index="2">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G8</td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="0">
                                <span class="prize-number font-medium" data-prize="g8" data-province="0" data-digits="2"><span class="spinning-placeholder">••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="1">
                                <span class="prize-number font-medium" data-prize="g8" data-province="1" data-digits="2"><span class="spinning-placeholder">••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col hidden" data-province-index="2">
                                <span class="prize-number font-medium" data-prize="g8" data-province="2" data-digits="2"><span class="spinning-placeholder">••</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G7</td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="0">
                                <span class="prize-number font-medium" data-prize="g7" data-province="0" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="1">
                                <span class="prize-number font-medium" data-prize="g7" data-province="1" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col hidden" data-province-index="2">
                                <span class="prize-number font-medium" data-prize="g7" data-province="2" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G6</td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col" data-province-index="0">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g6-0" data-province="0" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-1" data-province="0" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-2" data-province="0" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                </div>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col" data-province-index="1">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g6-0" data-province="1" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-1" data-province="1" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-2" data-province="1" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                </div>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col hidden" data-province-index="2">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g6-0" data-province="2" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-1" data-province="2" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-2" data-province="2" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G5</td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="0">
                                <span class="prize-number font-medium" data-prize="g5" data-province="0" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="1">
                                <span class="prize-number font-medium" data-prize="g5" data-province="1" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col hidden" data-province-index="2">
                                <span class="prize-number font-medium" data-prize="g5" data-province="2" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G4</td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col" data-province-index="0">
                                <div class="flex flex-col gap-0.5">
                                    @for($i = 0; $i < 7; $i++)
                                        <span class="prize-number font-medium" data-prize="g4-{{ $i }}" data-province="0" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    @endfor
                                </div>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col" data-province-index="1">
                                <div class="flex flex-col gap-0.5">
                                    @for($i = 0; $i < 7; $i++)
                                        <span class="prize-number font-medium" data-prize="g4-{{ $i }}" data-province="1" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    @endfor
                                </div>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col hidden" data-province-index="2">
                                <div class="flex flex-col gap-0.5">
                                    @for($i = 0; $i < 7; $i++)
                                        <span class="prize-number font-medium" data-prize="g4-{{ $i }}" data-province="2" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    @endfor
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G3</td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col" data-province-index="0">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g3-0" data-province="0" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g3-1" data-province="0" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                </div>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col" data-province-index="1">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g3-0" data-province="1" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g3-1" data-province="1" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                </div>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col hidden" data-province-index="2">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g3-0" data-province="2" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g3-1" data-province="2" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G2</td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="0">
                                <span class="prize-number font-semibold" data-prize="g2" data-province="0" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="1">
                                <span class="prize-number font-semibold" data-prize="g2" data-province="1" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col hidden" data-province-index="2">
                                <span class="prize-number font-semibold" data-prize="g2" data-province="2" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G1</td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="0">
                                <span class="prize-number text-lg font-bold text-blue-700" data-prize="g1" data-province="0" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col" data-province-index="1">
                                <span class="prize-number text-lg font-bold text-blue-700" data-prize="g1" data-province="1" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-1 px-2 text-center province-col hidden" data-province-index="2">
                                <span class="prize-number text-lg font-bold text-blue-700" data-prize="g1" data-province="2" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                        </tr>
                        <tr class="bg-[#fff8dc]">
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center text-red-600">ĐB</td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-col" data-province-index="0">
                                <span class="prize-number text-xl font-bold text-red-600" data-prize="db" data-province="0" data-digits="6"><span class="spinning-placeholder">••••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-col" data-province-index="1">
                                <span class="prize-number text-xl font-bold text-red-600" data-prize="db" data-province="1" data-digits="6"><span class="spinning-placeholder">••••••</span></span>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-col hidden" data-province-index="2">
                                <span class="prize-number text-xl font-bold text-red-600" data-prize="db" data-province="2" data-digits="6"><span class="spinning-placeholder">••••••</span></span>
                            </td>
                        </tr>
                    @elseif($activeTab === 'xsmn')
                        <!-- XSMN Multi-Province Prize Structure (4 columns for Saturday) -->
                        <tr class="bg-[#fffde7]">
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center w-14 bg-[#fff8dc]">Tỉnh</td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell" data-province-index="0">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell" data-province-index="1">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell" data-province-index="2">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                            <td class="border border-gray-300 py-2 px-3 text-center province-header-cell hidden" data-province-index="3">
                                <a href="#" class="text-[#0066cc] hover:text-[#ff6600] font-medium province-header-link"></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G8</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <span class="prize-number font-medium" data-prize="g8" data-province="{{ $p }}" data-digits="2"><span class="spinning-placeholder">••</span></span>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G7</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <span class="prize-number font-medium" data-prize="g7" data-province="{{ $p }}" data-digits="3"><span class="spinning-placeholder">•••</span></span>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G6</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g6-0" data-province="{{ $p }}" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-1" data-province="{{ $p }}" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g6-2" data-province="{{ $p }}" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                                </div>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G5</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <span class="prize-number font-medium" data-prize="g5" data-province="{{ $p }}" data-digits="4"><span class="spinning-placeholder">••••</span></span>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G4</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <div class="flex flex-col gap-0.5">
                                    @for($i = 0; $i < 7; $i++)
                                        <span class="prize-number font-medium" data-prize="g4-{{ $i }}" data-province="{{ $p }}" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    @endfor
                                </div>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G3</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center text-xs province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <div class="flex flex-col gap-0.5">
                                    <span class="prize-number font-medium" data-prize="g3-0" data-province="{{ $p }}" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                    <span class="prize-number font-medium" data-prize="g3-1" data-province="{{ $p }}" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                                </div>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G2</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <span class="prize-number font-semibold" data-prize="g2" data-province="{{ $p }}" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                            @endfor
                        </tr>
                        <tr>
                            <td class="border border-gray-300 py-1 px-2 font-bold text-center bg-gray-50 text-sm">G1</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-1 px-2 text-center province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <span class="prize-number text-lg font-bold text-blue-700" data-prize="g1" data-province="{{ $p }}" data-digits="5"><span class="spinning-placeholder">•••••</span></span>
                            </td>
                            @endfor
                        </tr>
                        <tr class="bg-[#fff8dc]">
                            <td class="border border-gray-300 py-2 px-3 font-bold text-center text-red-600">ĐB</td>
                            @for($p = 0; $p < 4; $p++)
                            <td class="border border-gray-300 py-2 px-3 text-center province-col {{ $p >= 3 ? 'hidden' : '' }}" data-province-index="{{ $p }}">
                                <span class="prize-number text-xl font-bold text-red-600" data-prize="db" data-province="{{ $p }}" data-digits="6"><span class="spinning-placeholder">••••••</span></span>
                            </td>
                            @endfor
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Filter Radio Buttons -->
        <div class="flex items-center gap-4 mb-6 text-sm">
            <span class="text-gray-700">Hiển thị:</span>
            <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio" name="digitFilter" value="all" checked class="text-[#ff6600]">
                <span>Tất cả</span>
            </label>
            <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio" name="digitFilter" value="2" class="text-[#ff6600]">
                <span>2 số cuối</span>
            </label>
            <label class="flex items-center gap-1 cursor-pointer">
                <input type="radio" name="digitFilter" value="3" class="text-[#ff6600]">
                <span>3 số cuối</span>
            </label>
        </div>

        <!-- Lô Tô Section -->
        <div class="mb-6" id="lotoSection">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Bảng Lô Tô</h3>
            @if($activeTab === 'xsmt')
                <!-- XSMT Multi-Province Lô Tô Table (3 columns) -->
                <table class="w-full border-collapse text-sm loto-table" id="lotoTableXsmt">
                    <thead>
                        <tr class="bg-[#fff8dc]">
                            <th class="border border-gray-300 py-2 px-2 text-center w-10">Đầu</th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header" data-province-index="0"></th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header" data-province-index="1"></th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header hidden" data-province-index="2"></th>
                        </tr>
                    </thead>
                    <tbody id="lotoTableBody">
                        @for($i = 0; $i < 10; $i++)
                            <tr>
                                <td class="border border-gray-300 py-1 px-2 text-center font-bold bg-gray-50">{{ $i }}</td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col" data-province-index="0" data-dau="{{ $i }}"></td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col" data-province-index="1" data-dau="{{ $i }}"></td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col hidden" data-province-index="2" data-dau="{{ $i }}"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            @elseif($activeTab === 'xsmn')
                <!-- XSMN Multi-Province Lô Tô Table (4 columns) -->
                <table class="w-full border-collapse text-sm loto-table" id="lotoTableXsmn">
                    <thead>
                        <tr class="bg-[#fff8dc]">
                            <th class="border border-gray-300 py-2 px-2 text-center w-10">Đầu</th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header" data-province-index="0"></th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header" data-province-index="1"></th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header" data-province-index="2"></th>
                            <th class="border border-gray-300 py-2 px-2 text-center loto-province-header hidden" data-province-index="3"></th>
                        </tr>
                    </thead>
                    <tbody id="lotoTableBodyXsmn">
                        @for($i = 0; $i < 10; $i++)
                            <tr>
                                <td class="border border-gray-300 py-1 px-2 text-center font-bold bg-gray-50">{{ $i }}</td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col" data-province-index="0" data-dau="{{ $i }}"></td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col" data-province-index="1" data-dau="{{ $i }}"></td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col" data-province-index="2" data-dau="{{ $i }}"></td>
                                <td class="border border-gray-300 py-1 px-2 loto-province-col hidden" data-province-index="3" data-dau="{{ $i }}"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            @else
                <!-- XSMB Standard Lô Tô Table -->
                <table class="w-full border-collapse text-sm loto-table">
                    <thead>
                        <tr class="bg-[#fff8dc]">
                            <th class="border border-gray-300 py-2 px-3 text-center w-12">Đầu</th>
                            <th class="border border-gray-300 py-2 px-3 text-center">Lô tô</th>
                            <th class="border border-gray-300 py-2 px-3 text-center w-12">Đuôi</th>
                            <th class="border border-gray-300 py-2 px-3 text-center">Lô tô</th>
                        </tr>
                    </thead>
                    <tbody id="lotoTableBody">
                        @for($i = 0; $i < 10; $i++)
                            <tr>
                                <td class="border border-gray-300 py-1 px-2 text-center font-bold bg-gray-50">{{ $i }}</td>
                                <td class="border border-gray-300 py-1 px-2 loto-dau-{{ $i }}"></td>
                                <td class="border border-gray-300 py-1 px-2 text-center font-bold bg-gray-50">{{ $i }}</td>
                                <td class="border border-gray-300 py-1 px-2 loto-duoi-{{ $i }}"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Info Notice -->
        <div class="bg-yellow-50 border border-yellow-200 rounded p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm text-yellow-800">
                    <p class="font-medium">Lưu ý:</p>
                    <p class="mt-1">Đây chỉ là kết quả mô phỏng ngẫu nhiên, không phải kết quả xổ số chính thức. Kết quả chỉ mang tính chất tham khảo và giải trí.</p>
                </div>
            </div>
        </div>

        <!-- SEO Content -->
        <div class="prose prose-sm max-w-none">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Quay thử xổ số {{ $regionName }} là gì?</h2>
            <p class="text-gray-700 mb-4">
                Quay thử xổ số {{ $regionName }} là công cụ mô phỏng kết quả xổ số với các số ngẫu nhiên. Chức năng này giúp người chơi
                có thể thử vận may, xem trước kết quả mô phỏng trước khi có kết quả xổ số chính thức được công bố.
            </p>

            @if($activeTab === 'xsmt' && isset($scheduleData))
                <!-- XSMT Weekly Schedule -->
                <h3 class="text-base font-bold text-gray-800 mb-2">Lịch xổ thử Miền Trung trong tuần</h3>
                <ul class="text-gray-700 mb-4 space-y-1">
                    @foreach($scheduleData as $day)
                        <li>
                            <span class="font-medium">{{ $day['dayName'] }}:</span>
                            @foreach($day['provinces'] as $index => $province)
                                <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $province->slug]) }}" class="text-[#0066cc] hover:text-[#ff6600]">{{ $province->name }}</a>@if($index < count($day['provinces']) - 1), @endif
                            @endforeach
                        </li>
                    @endforeach
                </ul>
            @elseif($activeTab === 'xsmn' && isset($scheduleData))
                <!-- XSMN Weekly Schedule -->
                <h3 class="text-base font-bold text-gray-800 mb-2">Lịch xổ thử Miền Nam trong tuần</h3>
                <ul class="text-gray-700 mb-4 space-y-1">
                    @foreach($scheduleData as $day)
                        <li>
                            <span class="font-medium">{{ $day['dayName'] }}:</span>
                            @foreach($day['provinces'] as $index => $province)
                                <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $province->slug]) }}" class="text-[#0066cc] hover:text-[#ff6600]">{{ $province->name }}</a>@if($index < count($day['provinces']) - 1), @endif
                            @endforeach
                        </li>
                    @endforeach
                </ul>
            @endif

            <h3 class="text-base font-bold text-gray-800 mb-2">Hướng dẫn sử dụng</h3>
            <ol class="list-decimal list-inside text-gray-700 mb-4 space-y-1">
                @if($activeTab === 'xsmt' || $activeTab === 'xsmn')
                    <li>Chọn ngày trong tuần để xem danh sách tỉnh quay số</li>
                    <li>Chọn tỉnh thành muốn quay thử từ danh sách</li>
                    <li>Nhấn nút "Bắt đầu quay" để mô phỏng kết quả các tỉnh cùng ngày</li>
                    <li>Xem kết quả mô phỏng và bảng lô tô theo từng tỉnh</li>
                @else
                    <li>Chọn tỉnh thành muốn quay thử từ danh sách</li>
                    <li>Nhấn nút "Bắt đầu quay" để bắt đầu quá trình mô phỏng</li>
                    <li>Đợi quá trình quay số hoàn tất (khoảng 30-45 giây)</li>
                    <li>Xem kết quả mô phỏng và bảng lô tô</li>
                @endif
            </ol>

            <h3 class="text-base font-bold text-gray-800 mb-2">Lưu ý quan trọng</h3>
            <ul class="list-disc list-inside text-gray-700 space-y-1">
                <li>Kết quả quay thử hoàn toàn ngẫu nhiên, không liên quan đến kết quả xổ số thực tế</li>
                <li>Chức năng này chỉ mang tính chất giải trí, không nên dùng để đặt cược</li>
                <li>Kết quả xổ số chính thức được công bố tại các kênh chính thống</li>
            </ul>
        </div>
    </div>
@endsection

@section('right-sidebar')
    <x-lottery-sidebar
        :northProvinces="$northProvinces"
        :centralProvinces="$centralProvinces"
        :southProvinces="$southProvinces"
        :showCalendar="true"
        :showProvinces="true"
        region="{{ $activeTab }}"
    />
@endsection

@section('scripts')
<style>
    /* Spinning animation for placeholders */
    @keyframes spin-dot {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 1; }
    }

    .spinning-placeholder {
        display: inline-block;
        letter-spacing: 2px;
    }

    .spinning-placeholder.animating {
        animation: spin-dot 0.3s infinite;
    }

    .trial-prize-table .prize-number {
        font-family: 'Courier New', monospace;
    }

    .loto-table td {
        font-family: 'Courier New', monospace;
    }

    .number-revealed {
        color: #dc2626;
        font-weight: bold;
    }

    .loto-number {
        color: #dc2626;
        font-weight: bold;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDrawBtn = document.getElementById('startDrawBtn');
    const provinceSelect = document.getElementById('provinceSelect');
    const daySelect = document.getElementById('daySelect');
    const prizeTable = document.getElementById('prizeTable');
    const lotoSection = document.getElementById('lotoSection');
    const activeTab = '{{ $activeTab }}';

    // Schedule data
    @if($activeTab === 'xsmt' && isset($xsmtSchedule))
    const schedule = @json($xsmtSchedule);
    const allProvinces = @json($provinces->map(function($p) { return ['id' => $p->id, 'name' => $p->name, 'slug' => $p->slug]; })->values());
    const maxColumns = 3;
    const regionSlug = 'xsmt';
    @elseif($activeTab === 'xsmn' && isset($xsmnSchedule))
    const schedule = @json($xsmnSchedule);
    const allProvinces = @json($provinces->map(function($p) { return ['id' => $p->id, 'name' => $p->name, 'slug' => $p->slug]; })->values());
    const maxColumns = 4;
    const regionSlug = 'xsmn';
    @else
    const schedule = {};
    const allProvinces = [];
    const maxColumns = 3;
    const regionSlug = '';
    @endif

    // Prize structure configurations
    const XSMB_PRIZES = {
        'g1': { digits: 5, delay: 0 },
        'g2-0': { digits: 5, delay: 1500 },
        'g2-1': { digits: 5, delay: 2000 },
        'g3-0': { digits: 5, delay: 3500 },
        'g3-1': { digits: 5, delay: 4000 },
        'g3-2': { digits: 5, delay: 4500 },
        'g3-3': { digits: 5, delay: 5000 },
        'g3-4': { digits: 5, delay: 5500 },
        'g3-5': { digits: 5, delay: 6000 },
        'g4-0': { digits: 4, delay: 7500 },
        'g4-1': { digits: 4, delay: 8000 },
        'g4-2': { digits: 4, delay: 8500 },
        'g4-3': { digits: 4, delay: 9000 },
        'g5-0': { digits: 4, delay: 10500 },
        'g5-1': { digits: 4, delay: 11000 },
        'g5-2': { digits: 4, delay: 11500 },
        'g5-3': { digits: 4, delay: 12000 },
        'g5-4': { digits: 4, delay: 12500 },
        'g5-5': { digits: 4, delay: 13000 },
        'g6-0': { digits: 3, delay: 14500 },
        'g6-1': { digits: 3, delay: 15000 },
        'g6-2': { digits: 3, delay: 15500 },
        'g7-0': { digits: 2, delay: 17000 },
        'g7-1': { digits: 2, delay: 17500 },
        'g7-2': { digits: 2, delay: 18000 },
        'g7-3': { digits: 2, delay: 18500 },
        'db': { digits: 5, delay: 20000 },
    };

    const XSMT_MN_PRIZES = {
        'g1': { digits: 5, delay: 0 },
        'g2': { digits: 5, delay: 1500 },
        'g3-0': { digits: 5, delay: 3000 },
        'g3-1': { digits: 5, delay: 3500 },
        'g4-0': { digits: 5, delay: 5000 },
        'g4-1': { digits: 5, delay: 5500 },
        'g4-2': { digits: 5, delay: 6000 },
        'g4-3': { digits: 5, delay: 6500 },
        'g4-4': { digits: 5, delay: 7000 },
        'g4-5': { digits: 5, delay: 7500 },
        'g4-6': { digits: 5, delay: 8000 },
        'g5': { digits: 4, delay: 9500 },
        'g6-0': { digits: 4, delay: 11000 },
        'g6-1': { digits: 4, delay: 11500 },
        'g6-2': { digits: 4, delay: 12000 },
        'g7': { digits: 3, delay: 13500 },
        'g8': { digits: 2, delay: 15000 },
        'db': { digits: 6, delay: 17000 },
    };

    const prizeConfig = activeTab === 'xsmb' ? XSMB_PRIZES : XSMT_MN_PRIZES;

    // Store generated numbers per province for XSMT multi-province support
    let generatedNumbers = [];
    let generatedNumbersByProvince = {};
    let activeProvinces = [];
    let isDrawing = false;

    // Helper to clear all children from an element safely
    function clearChildren(element) {
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    }

    // Generate random number with specified digits
    function generateNumber(digits) {
        const max = Math.pow(10, digits) - 1;
        return String(Math.floor(Math.random() * (max + 1))).padStart(digits, '0');
    }

    // Create spinning animation effect
    function spinNumber(element, targetNumber, duration) {
        duration = duration || 1000;
        const digits = targetNumber.length;
        let elapsed = 0;
        const interval = 50;

        const spinner = setInterval(function() {
            elapsed += interval;
            if (elapsed >= duration) {
                clearInterval(spinner);
                element.textContent = targetNumber;
                element.classList.add('number-revealed');
                return;
            }
            element.textContent = generateNumber(digits);
        }, interval);
    }

    // Get provinces for a specific day (XSMT/XSMN)
    function getProvincesForDay(dayValue) {
        const dayProvinceNames = schedule[dayValue] || [];
        return allProvinces.filter(function(p) {
            return dayProvinceNames.indexOf(p.name) !== -1;
        });
    }

    // Update province dropdown based on selected day (XSMT/XSMN)
    function updateProvinceDropdown(dayValue) {
        if ((activeTab !== 'xsmt' && activeTab !== 'xsmn') || !daySelect) return;

        const provincesForDay = getProvincesForDay(dayValue);
        clearChildren(provinceSelect);

        provincesForDay.forEach(function(province) {
            const option = document.createElement('option');
            option.value = province.id;
            option.dataset.name = province.name;
            option.dataset.slug = province.slug;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });

        // Update active provinces and table columns
        activeProvinces = provincesForDay;
        updateProvinceColumns();
    }

    // Update province columns visibility and headers (XSMT/XSMN)
    function updateProvinceColumns() {
        if (activeTab !== 'xsmt' && activeTab !== 'xsmn') return;

        const numProvinces = activeProvinces.length;

        // Update province header cells and columns
        for (let i = 0; i < maxColumns; i++) {
            const headerCells = document.querySelectorAll('.province-header-cell[data-province-index="' + i + '"]');
            const dataCells = document.querySelectorAll('.province-col[data-province-index="' + i + '"]');
            const lotoHeaders = document.querySelectorAll('.loto-province-header[data-province-index="' + i + '"]');
            const lotoCells = document.querySelectorAll('.loto-province-col[data-province-index="' + i + '"]');

            if (i < numProvinces) {
                // Show column
                headerCells.forEach(function(cell) {
                    cell.classList.remove('hidden');
                    const link = cell.querySelector('.province-header-link');
                    if (link && activeProvinces[i]) {
                        link.textContent = activeProvinces[i].name;
                        link.href = '/' + regionSlug + '/' + encodeURIComponent(activeProvinces[i].slug);
                    }
                });
                dataCells.forEach(function(cell) { cell.classList.remove('hidden'); });
                lotoHeaders.forEach(function(cell) {
                    cell.classList.remove('hidden');
                    cell.textContent = activeProvinces[i] ? activeProvinces[i].name : '';
                });
                lotoCells.forEach(function(cell) { cell.classList.remove('hidden'); });
            } else {
                // Hide column
                headerCells.forEach(function(cell) { cell.classList.add('hidden'); });
                dataCells.forEach(function(cell) { cell.classList.add('hidden'); });
                lotoHeaders.forEach(function(cell) { cell.classList.add('hidden'); });
                lotoCells.forEach(function(cell) { cell.classList.add('hidden'); });
            }
        }
    }

    // Start draw for XSMT/XSMN (multi-province)
    function startDrawMultiProvince() {
        if (isDrawing) return;
        isDrawing = true;

        generatedNumbers = [];
        generatedNumbersByProvince = {};
        resetPrizeTable();
        resetLotoTableMultiProvince();

        startDrawBtn.disabled = true;
        startDrawBtn.textContent = 'Đang quay...';

        // Generate numbers for each province
        activeProvinces.forEach(function(province, pIndex) {
            generatedNumbersByProvince[pIndex] = [];
            const allNumbers = {};

            Object.keys(prizeConfig).forEach(function(key) {
                allNumbers[key] = generateNumber(prizeConfig[key].digits);
                generatedNumbersByProvince[pIndex].push(allNumbers[key]);
            });

            // Animate each prize for this province
            Object.keys(prizeConfig).forEach(function(key) {
                const config = prizeConfig[key];
                const element = document.querySelector('[data-prize="' + key + '"][data-province="' + pIndex + '"]');

                if (element) {
                    setTimeout(function() {
                        const placeholder = element.querySelector('.spinning-placeholder');
                        if (placeholder) {
                            placeholder.classList.add('animating');
                        }
                    }, Math.max(0, config.delay - 500));

                    setTimeout(function() {
                        spinNumber(element, allNumbers[key], 800);
                    }, config.delay);
                }
            });
        });

        // Calculate loto after all draws complete
        const delays = Object.values(prizeConfig).map(function(c) { return c.delay; });
        const maxDelayTime = Math.max.apply(null, delays) + 1500;
        setTimeout(function() {
            calculateLotoMultiProvince();
            isDrawing = false;
            startDrawBtn.disabled = false;
            startDrawBtn.textContent = 'Bắt đầu quay';
        }, maxDelayTime);
    }

    // Start draw for XSMB (single province)
    function startDrawSingle() {
        if (isDrawing) return;
        isDrawing = true;

        generatedNumbers = [];
        resetPrizeTable();
        resetLotoTable();

        startDrawBtn.disabled = true;
        startDrawBtn.textContent = 'Đang quay...';

        const allNumbers = {};
        Object.keys(prizeConfig).forEach(function(key) {
            allNumbers[key] = generateNumber(prizeConfig[key].digits);
            generatedNumbers.push(allNumbers[key]);
        });

        Object.keys(prizeConfig).forEach(function(key) {
            const config = prizeConfig[key];
            const element = document.querySelector('[data-prize="' + key + '"]');

            if (element) {
                setTimeout(function() {
                    const placeholder = element.querySelector('.spinning-placeholder');
                    if (placeholder) {
                        placeholder.classList.add('animating');
                    }
                }, Math.max(0, config.delay - 500));

                setTimeout(function() {
                    spinNumber(element, allNumbers[key], 800);
                }, config.delay);
            }
        });

        const delays = Object.values(prizeConfig).map(function(c) { return c.delay; });
        const maxDelay = Math.max.apply(null, delays) + 1500;
        setTimeout(function() {
            calculateLoto();
            isDrawing = false;
            startDrawBtn.disabled = false;
            startDrawBtn.textContent = 'Bắt đầu quay';
        }, maxDelay);
    }

    // Main start draw function
    function startDraw() {
        if (activeTab === 'xsmt' || activeTab === 'xsmn') {
            startDrawMultiProvince();
        } else {
            startDrawSingle();
        }
    }

    // Reset prize table
    function resetPrizeTable() {
        document.querySelectorAll('.prize-number').forEach(function(el) {
            const digits = parseInt(el.dataset.digits) || 5;
            el.textContent = '';
            const placeholder = document.createElement('span');
            placeholder.className = 'spinning-placeholder';
            placeholder.textContent = '•'.repeat(digits);
            el.appendChild(placeholder);
            el.classList.remove('number-revealed');
        });
    }

    // Reset loto table (XSMB/XSMN)
    function resetLotoTable() {
        for (let i = 0; i < 10; i++) {
            const dauCell = document.querySelector('.loto-dau-' + i);
            const duoiCell = document.querySelector('.loto-duoi-' + i);
            if (dauCell) dauCell.textContent = '';
            if (duoiCell) duoiCell.textContent = '';
        }
    }

    // Reset loto table (XSMT/XSMN multi-province)
    function resetLotoTableMultiProvince() {
        for (let i = 0; i < 10; i++) {
            for (let p = 0; p < maxColumns; p++) {
                const cell = document.querySelector('.loto-province-col[data-province-index="' + p + '"][data-dau="' + i + '"]');
                if (cell) cell.textContent = '';
            }
        }
    }

    // Calculate loto (XSMB/XSMN)
    function calculateLoto() {
        const dauNumbers = {};
        const duoiNumbers = {};

        for (let i = 0; i < 10; i++) {
            dauNumbers[i] = [];
            duoiNumbers[i] = [];
        }

        generatedNumbers.forEach(function(num) {
            const twoDigit = num.slice(-2);
            const dau = parseInt(twoDigit[0]);
            const duoi = parseInt(twoDigit[1]);

            dauNumbers[dau].push(twoDigit);
            duoiNumbers[duoi].push(twoDigit);
        });

        for (let i = 0; i < 10; i++) {
            const dauCell = document.querySelector('.loto-dau-' + i);
            const duoiCell = document.querySelector('.loto-duoi-' + i);

            if (dauCell) {
                dauCell.textContent = '';
                dauNumbers[i].forEach(function(n, idx) {
                    const span = document.createElement('span');
                    span.className = 'loto-number';
                    span.textContent = n;
                    dauCell.appendChild(span);
                    if (idx < dauNumbers[i].length - 1) {
                        dauCell.appendChild(document.createTextNode(', '));
                    }
                });
            }

            if (duoiCell) {
                duoiCell.textContent = '';
                duoiNumbers[i].forEach(function(n, idx) {
                    const span = document.createElement('span');
                    span.className = 'loto-number';
                    span.textContent = n;
                    duoiCell.appendChild(span);
                    if (idx < duoiNumbers[i].length - 1) {
                        duoiCell.appendChild(document.createTextNode(', '));
                    }
                });
            }
        }
    }

    // Calculate loto (XSMT/XSMN multi-province)
    function calculateLotoMultiProvince() {
        activeProvinces.forEach(function(province, pIndex) {
            const dauNumbers = {};
            for (let i = 0; i < 10; i++) {
                dauNumbers[i] = [];
            }

            const numbers = generatedNumbersByProvince[pIndex] || [];
            numbers.forEach(function(num) {
                const twoDigit = num.slice(-2);
                const dau = parseInt(twoDigit[0]);
                dauNumbers[dau].push(twoDigit);
            });

            for (let i = 0; i < 10; i++) {
                const cell = document.querySelector('.loto-province-col[data-province-index="' + pIndex + '"][data-dau="' + i + '"]');
                if (cell) {
                    cell.textContent = '';
                    dauNumbers[i].forEach(function(n, idx) {
                        const span = document.createElement('span');
                        span.className = 'loto-number';
                        span.textContent = n;
                        cell.appendChild(span);
                        if (idx < dauNumbers[i].length - 1) {
                            cell.appendChild(document.createTextNode(', '));
                        }
                    });
                }
            }
        });
    }

    // Handle digit filter
    document.querySelectorAll('input[name="digitFilter"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const filter = this.value;
            document.querySelectorAll('.prize-number.number-revealed').forEach(function(el) {
                const fullNumber = el.textContent;
                if (filter === 'all') {
                    if (el.dataset.full) {
                        el.textContent = el.dataset.full;
                    }
                } else {
                    const digits = parseInt(filter);
                    if (!el.dataset.full) {
                        el.dataset.full = fullNumber;
                    }
                    el.textContent = el.dataset.full.slice(-digits);
                }
            });

            if (filter === 'all') {
                document.querySelectorAll('.prize-number[data-full]').forEach(function(el) {
                    el.textContent = el.dataset.full;
                    delete el.dataset.full;
                });
            }
        });
    });

    // Quick province links
    document.querySelectorAll('.quick-province-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const name = this.dataset.name;

            if ((activeTab === 'xsmt' || activeTab === 'xsmn') && daySelect) {
                // Find which day this province belongs to and switch
                for (const day in schedule) {
                    if (schedule[day].indexOf(name) !== -1) {
                        daySelect.value = day;
                        updateProvinceDropdown(day);
                        break;
                    }
                }
            }

            // Verify province exists in dropdown before setting value
            const optionExists = provinceSelect.querySelector('option[value="' + id + '"]');
            if (optionExists) {
                provinceSelect.value = id;
            }
            startDraw();
        });
    });

    // Day selector change handler (XSMT/XSMN)
    if (daySelect) {
        daySelect.addEventListener('change', function() {
            updateProvinceDropdown(this.value);
        });

        // Initialize on page load
        updateProvinceDropdown(daySelect.value);
    }

    // Event listeners
    startDrawBtn.addEventListener('click', startDraw);
});
</script>
@endsection
