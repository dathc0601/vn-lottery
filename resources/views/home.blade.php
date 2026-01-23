@extends('layouts.app')

@section('title', 'Kết quả xổ số hôm nay - XSKT.VN')

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content (65%) -->
        <div class="flex-1 lg:w-[100% - 275px]">

            <!-- Quick Region Links Tabs -->
            <div class="grid md:grid-cols-3 gap-3 mb-4">
                <!-- KQXSMB Tab -->
                <div class="bg-[#FFF9E6] border border-gray-300 p-3">
                    <h3 class="font-bold text-gray-800 mb-2 text-sm">KQXSMB - Xổ số miền Bắc</h3>
                    <ul class="space-y-1 text-xs">
                        @foreach($northProvinces->take(6) as $province)
                            <li>
                                <a href="{{ route('province.detail', ['region' => 'xsmb', 'slug' => $province->slug]) }}"
                                   class="text-blue-600 hover:underline">
                                    {{ $province->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- KQXSMT Tab -->
                <div class="bg-[#FFF9E6] border border-gray-300 p-3">
                    <h3 class="font-bold text-gray-800 mb-2 text-sm">KQXSMT - Xổ số miền Trung</h3>
                    <ul class="space-y-1 text-xs">
                        @foreach($centralProvinces->take(6) as $province)
                            <li>
                                <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $province->slug]) }}"
                                   class="text-blue-600 hover:underline">
                                    {{ $province->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- KQXSMN Tab -->
                <div class="bg-[#FFF9E6] border border-gray-300 p-3">
                    <h3 class="font-bold text-gray-800 mb-2 text-sm">KQXSMN - Xổ số miền Nam</h3>
                    <ul class="space-y-1 text-xs">
                        @foreach($southProvinces->take(6) as $province)
                            <li>
                                <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $province->slug]) }}"
                                   class="text-blue-600 hover:underline">
                                    {{ $province->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- XSMB Results Section -->
            @if($northResults->count() > 0)
                @foreach($northResults as $result)
                <div class="mb-6">
                    <!-- Section Header -->
                    <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                        <h2 class="font-bold text-gray-800 text-base inline-block">
                            KQXS MB - Kết quả xổ số Miền Bắc
                        </h2>
                        <span class="text-sm text-gray-600 ml-3">
                            {{ \Carbon\Carbon::parse($result->draw_date)->format('d/m/Y') }}
                        </span>
                        <div class="mt-1 text-sm">
                            <a href="{{ route('xsmb') }}" class="text-blue-600 hover:underline">XSMB</a>
                            <span class="mx-1">|</span>
                            <a href="{{ route('xsmb') }}" class="text-blue-600 hover:underline">XSMB {{ \Carbon\Carbon::parse($result->draw_date)->isoFormat('dddd') }}</a>
                            <span class="mx-1">|</span>
                            <a href="{{ route('xsmb') }}" class="text-blue-600 hover:underline">XSMB {{ \Carbon\Carbon::parse($result->draw_date)->format('d/m/Y') }}</a>
                        </div>
                    </div>

                    <!-- Prize Table -->
                    <div class="bg-white border border-gray-300 mb-3">
                        <table class="w-full border-collapse">
                            <tbody>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-3 font-bold text-gray-800 border-r border-gray-300 w-32 bg-gray-50">ĐB</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-3xl font-bold text-red-600">{{ $result->prize_special }}</span>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G1</td>
                                    <td class="px-4 py-2 text-center text-lg font-semibold">{{ $result->prize_1 }}</td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G2</td>
                                    <td class="px-4 py-2 text-center">{{ str_replace(',', ' - ', $result->prize_2) }}</td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G3</td>
                                    <td class="px-4 py-2 text-center text-sm">{{ str_replace(',', ' - ', $result->prize_3) }}</td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G4</td>
                                    <td class="px-4 py-2 text-center text-sm">{{ str_replace(',', ' - ', $result->prize_4) }}</td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G5</td>
                                    <td class="px-4 py-2 text-center text-sm">{{ str_replace(',', ' - ', $result->prize_5) }}</td>
                                </tr>
                                <tr class="border-b border-gray-300">
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G6</td>
                                    <td class="px-4 py-2 text-center text-sm">{{ str_replace(',', ' - ', $result->prize_6) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">G7</td>
                                    <td class="px-4 py-2 text-center text-sm">{{ str_replace(',', ' - ', $result->prize_7) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Display Options -->
                    <div class="mb-3">
                        <div class="flex gap-4 text-sm">
                            <label class="inline-flex items-center">
                                <input type="radio" name="display_mode" value="all" checked class="mr-1">
                                <span class="text-gray-700">Tất cả</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="display_mode" value="2_digits" class="mr-1">
                                <span class="text-gray-700">2 số cuối</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="display_mode" value="3_digits" class="mr-1">
                                <span class="text-gray-700">3 số cuối</span>
                            </label>
                        </div>
                    </div>

                    <!-- Lotto Statistics Table -->
                    <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-2">
                        <h3 class="font-bold text-gray-800 text-sm">
                            Bảng loto miền Bắc / Lô XSMB {{ \Carbon\Carbon::parse($result->draw_date)->isoFormat('dddd') }}
                        </h3>
                    </div>

                    <div class="bg-white border border-gray-300 mb-3">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-gray-300">
                                    <th class="px-2 py-2 text-left font-bold text-gray-800 border-r border-gray-300 w-16 bg-gray-50">Đầu</th>
                                    <th class="px-2 py-2 text-left font-bold text-gray-800 bg-gray-50">Lô tô</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Extract all numbers from prizes and organize by first digit
                                    $allNumbers = [];
                                    foreach(['prize_special', 'prize_1', 'prize_2', 'prize_3', 'prize_4', 'prize_5', 'prize_6', 'prize_7'] as $prize) {
                                        if($result->$prize) {
                                            $numbers = explode(',', $result->$prize);
                                            foreach($numbers as $num) {
                                                $num = trim($num);
                                                if(strlen($num) >= 2) {
                                                    $last2 = substr($num, -2);
                                                    $allNumbers[] = $last2;
                                                }
                                            }
                                        }
                                    }

                                    // Group by first digit
                                    $lotoByHead = [];
                                    for($i = 0; $i <= 9; $i++) {
                                        $lotoByHead[$i] = [];
                                    }
                                    foreach($allNumbers as $num) {
                                        $head = intval($num[0]);
                                        $lotoByHead[$head][] = $num;
                                    }
                                @endphp

                                @for($i = 0; $i <= 9; $i++)
                                    <tr class="border-b border-gray-300">
                                        <td class="px-2 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">{{ $i }}</td>
                                        <td class="px-2 py-2">
                                            @if(count($lotoByHead[$i]) > 0)
                                                {{ implode(', ', $lotoByHead[$i]) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <!-- Additional Statistics Links -->
                    <div class="text-sm space-y-1 mb-4">
                        <div class="text-blue-600 hover:underline cursor-pointer">▼ Xem thêm XSMB 60 ngày</div>
                        <div class="text-blue-600 hover:underline cursor-pointer">▼ Trải nghiệm Quay thử xổ số miền Bắc</div>
                        <div class="text-blue-600 hover:underline cursor-pointer">▼ Thống kê Bảng đặc biệt tuần XSMB</div>
                        <div class="text-blue-600 hover:underline cursor-pointer">▼ Thống kê Loto gan XSMB</div>
                    </div>
                </div>
                @endforeach
            @endif

            <!-- XSMN Results Section -->
            @if($southResults->count() > 0)
            <div class="mb-6">
                <!-- Section Header -->
                <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-gray-800 text-base">
                        KQXS MN - Kết quả xổ số Miền Nam - SXMN
                    </h2>
                    <div class="mt-1 text-sm">
                        <a href="{{ route('xsmn') }}" class="text-blue-600 hover:underline">XSMN</a>
                        <span class="mx-1">|</span>
                        <a href="{{ route('xsmn') }}" class="text-blue-600 hover:underline">XSMN {{ now()->isoFormat('dddd') }}</a>
                    </div>
                </div>

                <!-- Multi-Province Table -->
                <div class="bg-white border border-gray-300 overflow-x-auto mb-3">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 bg-gray-50">
                                <th class="px-2 py-2 text-left font-bold text-gray-800 border-r border-gray-300 w-16">Giải</th>
                                @foreach($southResults as $result)
                                    <th class="px-3 py-2 text-center font-bold text-gray-800 border-r border-gray-300">
                                        {{ $result->province->name }}<br>
                                        <span class="font-normal text-xs text-gray-600">{{ \Carbon\Carbon::parse($result->draw_date)->format('d/m') }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $prizes = [
                                    'G8' => 'prize_8',
                                    'G7' => 'prize_7',
                                    'G6' => 'prize_6',
                                    'G5' => 'prize_5',
                                    'G4' => 'prize_4',
                                    'G3' => 'prize_3',
                                    'G2' => 'prize_2',
                                    'G1' => 'prize_1',
                                    'ĐB' => 'prize_special',
                                ];
                            @endphp

                            @foreach($prizes as $label => $field)
                                <tr class="border-b border-gray-300">
                                    <td class="px-2 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">{{ $label }}</td>
                                    @foreach($southResults as $result)
                                        <td class="px-3 py-2 text-center border-r border-gray-300 {{ $label === 'ĐB' ? 'bg-red-50' : '' }}">
                                            @if(isset($result->$field) && $result->$field)
                                                <span class="{{ $label === 'ĐB' ? 'text-red-600 font-bold text-lg' : '' }}">
                                                    {{ str_replace(',', ', ', $result->$field) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- XSMT Results Section -->
            @if($centralResults->count() > 0)
            <div class="mb-6">
                <!-- Section Header -->
                <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-gray-800 text-base">
                        KQXS MT - Kết quả xổ số Miền Trung - SXMT
                    </h2>
                    <div class="mt-1 text-sm">
                        <a href="{{ route('xsmt') }}" class="text-blue-600 hover:underline">XSMT</a>
                        <span class="mx-1">|</span>
                        <a href="{{ route('xsmt') }}" class="text-blue-600 hover:underline">XSMT {{ now()->isoFormat('dddd') }}</a>
                    </div>
                </div>

                <!-- Multi-Province Table -->
                <div class="bg-white border border-gray-300 overflow-x-auto mb-3">
                    <table class="w-full border-collapse text-sm">
                        <thead>
                            <tr class="border-b border-gray-300 bg-gray-50">
                                <th class="px-2 py-2 text-left font-bold text-gray-800 border-r border-gray-300 w-16">Giải</th>
                                @foreach($centralResults as $result)
                                    <th class="px-3 py-2 text-center font-bold text-gray-800 border-r border-gray-300">
                                        {{ $result->province->name }}<br>
                                        <span class="font-normal text-xs text-gray-600">{{ \Carbon\Carbon::parse($result->draw_date)->format('d/m') }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prizes as $label => $field)
                                <tr class="border-b border-gray-300">
                                    <td class="px-2 py-2 font-bold text-gray-800 border-r border-gray-300 bg-gray-50">{{ $label }}</td>
                                    @foreach($centralResults as $result)
                                        <td class="px-3 py-2 text-center border-r border-gray-300 {{ $label === 'ĐB' ? 'bg-red-50' : '' }}">
                                            @if(isset($result->$field) && $result->$field)
                                                <span class="{{ $label === 'ĐB' ? 'text-red-600 font-bold text-lg' : '' }}">
                                                    {{ str_replace(',', ', ', $result->$field) }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Xổ số Mega 6/45 Section -->
            @if($vietlottResults['mega645'])
            <div class="mb-6">
                <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-gray-800 text-base inline-block">
                        Xổ số Mega 6/45
                    </h2>
                    <a href="{{ route('vietlott.mega645') }}" class="text-blue-600 hover:underline text-sm ml-3">
                        Xem thêm »
                    </a>
                </div>
                <x-vietlott.mega-result-card :result="$vietlottResults['mega645']" />
            </div>
            @endif

            <!-- Xổ số Power 6/55 Section -->
            @if($vietlottResults['power655'])
            <div class="mb-6">
                <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-gray-800 text-base inline-block">
                        Xổ số Power 6/55
                    </h2>
                    <a href="{{ route('vietlott.power655') }}" class="text-blue-600 hover:underline text-sm ml-3">
                        Xem thêm »
                    </a>
                </div>
                <x-vietlott.power-result-card :result="$vietlottResults['power655']" />
            </div>
            @endif

            <!-- Xổ số Max 3D Section -->
            @if($vietlottResults['max3d'])
            <div class="mb-6">
                <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-gray-800 text-base inline-block">
                        Xổ số Max 3D
                    </h2>
                    <a href="{{ route('vietlott.max3d') }}" class="text-blue-600 hover:underline text-sm ml-3">
                        Xem thêm »
                    </a>
                </div>
                <x-vietlott.max3d-result-card :result="$vietlottResults['max3d']" />
            </div>
            @endif

            <!-- Xổ số Max 3D Pro Section -->
            @if($vietlottResults['max3dpro'])
            <div class="mb-6">
                <div class="bg-[#FFF9E6] border border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-gray-800 text-base inline-block">
                        Xổ số Max 3D Pro
                    </h2>
                    <a href="{{ route('vietlott.max3dpro') }}" class="text-blue-600 hover:underline text-sm ml-3">
                        Xem thêm »
                    </a>
                </div>
                <x-vietlott.max3dpro-result-card :result="$vietlottResults['max3dpro']" />
            </div>
            @endif

            <!-- Information Section -->
            <div class="bg-white border border-gray-300 p-4 mb-6">
                <h2 class="font-bold text-gray-800 text-lg mb-3">Thông tin về xổ số kiến thiết</h2>
                <div class="text-sm text-gray-700 space-y-3">
                    <div>
                        <h3 class="font-bold text-gray-800 mb-1">Xổ số miền Bắc</h3>
                        <p>
                            Xổ số miền Bắc quay thưởng hàng ngày từ thứ 2 đến Chủ nhật vào lúc 18h15.
                            Kết quả xổ số được phát hành bởi Công ty Xổ số kiến thiết Hà Nội.
                            Mỗi kỳ quay có 27 giải thưởng từ giải Đặc biệt đến giải 7.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-1">Xổ số miền Trung</h3>
                        <p>
                            Xổ số miền Trung quay thưởng vào lúc 17h15 hàng ngày.
                            Mỗi ngày có từ 2-3 tỉnh quay thưởng. Cơ cấu giải thưởng gồm 18 giải từ giải Đặc biệt đến giải 8.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-1">Xổ số miền Nam</h3>
                        <p>
                            Xổ số miền Nam quay thưởng vào lúc 16h15 hàng ngày.
                            Mỗi ngày có từ 3-6 tỉnh quay thưởng cùng lúc. Cơ cấu giải thưởng gồm 18 giải từ giải Đặc biệt đến giải 8.
                        </p>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 mb-1">Kết quả xổ số</h3>
                        <p>
                            XSKT.VN cung cấp kết quả xổ số 3 miền nhanh nhất và chính xác nhất.
                            Kết quả được cập nhật ngay sau khi có công bố chính thức từ các đài xổ số.
                            Trang web cũng cung cấp các thống kê, dự đoán và công cụ dò vé số miễn phí.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="bg-white border border-gray-300 p-4 mb-6">
                <h2 class="font-bold text-gray-800 text-lg mb-3">❓ Câu hỏi thường gặp về XSKT</h2>
                <div class="space-y-3">
                    <details class="border-b border-gray-200 pb-3">
                        <summary class="font-semibold text-gray-800 cursor-pointer hover:text-orange-500 text-sm">
                            Xổ số kiến thiết là gì và có mục đích gì?
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-4">
                            Xổ số kiến thiết là hình thức xổ số do Nhà nước tổ chức, nhằm mục đích huy động vốn
                            cho các công trình kiến thiết công cộng, từ thiện và phúc lợi xã hội.
                        </p>
                    </details>

                    <details class="border-b border-gray-200 pb-3">
                        <summary class="font-semibold text-gray-800 cursor-pointer hover:text-orange-500 text-sm">
                            Mỗi ngày có bao nhiêu đài xổ số mở thưởng và vào thời gian nào?
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-4">
                            Miền Bắc: 1 đài (Hà Nội) quay hàng ngày lúc 18h15.<br>
                            Miền Trung: 2-3 tỉnh mỗi ngày, quay lúc 17h15.<br>
                            Miền Nam: 3-6 tỉnh mỗi ngày, quay lúc 16h15.
                        </p>
                    </details>

                    <details class="border-b border-gray-200 pb-3">
                        <summary class="font-semibold text-gray-800 cursor-pointer hover:text-orange-500 text-sm">
                            Cơ cấu giải thưởng xổ số kiến thiết ở ba miền như thế nào?
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-4">
                            Miền Bắc: 27 giải (ĐB, G1 đến G7).<br>
                            Miền Trung/Nam: 18 giải (ĐB, G1 đến G8).
                        </p>
                    </details>

                    <details class="border-b border-gray-200 pb-3">
                        <summary class="font-semibold text-gray-800 cursor-pointer hover:text-orange-500 text-sm">
                            Thời hạn lĩnh thưởng sau khi trúng số là bao lâu?
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-4">
                            Thời hạn lĩnh thưởng là 60 ngày kể từ ngày quay số mở thưởng.
                            Quá thời hạn này, vé trúng thưởng sẽ không được chấp nhận.
                        </p>
                    </details>

                    <details class="border-b border-gray-200 pb-3">
                        <summary class="font-semibold text-gray-800 cursor-pointer hover:text-orange-500 text-sm">
                            Ai được phép mua vé số kiến thiết tại Việt Nam?
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-4">
                            Mọi công dân từ đủ 18 tuổi trở lên đều được phép mua vé số kiến thiết.
                            Không giới hạn quốc tịch.
                        </p>
                    </details>

                    <details class="pb-3">
                        <summary class="font-semibold text-gray-800 cursor-pointer hover:text-orange-500 text-sm">
                            Làm sao để xem kết quả xổ số nhanh nhất?
                        </summary>
                        <p class="mt-2 text-sm text-gray-700 pl-4">
                            Truy cập XSKT.VN để xem kết quả xổ số nhanh nhất và chính xác nhất.
                            Kết quả được cập nhật ngay sau khi có công bố chính thức.
                        </p>
                    </details>
                </div>
            </div>

        </div>

        <!-- Sidebar (35%) -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
        />

    </div>
</div>
@endsection
