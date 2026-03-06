@extends('layouts.app')

@section('title', 'Kết quả xổ số hôm nay - XSKT.VN')

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content (65%) -->
        <div class="flex-1 lg:w-[100% - 275px]">

            <!-- Latest Predictions Section -->
            @php
                $predictionOrder = ['xsmn', 'xsmt', 'xsmb'];
                $hasPredictions = collect($predictionOrder)->contains(fn($s) => !empty($latestPredictions[$s]));
                $weekdays = [0 => 'CN', 1 => 'Thứ 2', 2 => 'Thứ 3', 3 => 'Thứ 4', 4 => 'Thứ 5', 5 => 'Thứ 6', 6 => 'Thứ 7'];
            @endphp
            @if($hasPredictions)
            <div class="mb-4 border border-gray-300">
                <div class="bg-[#ffe89f] border-b border-gray-300 px-4 py-2 mb-3">
                    <h2 class="font-bold text-base">
                        <a href="{{ route('prediction.index') }}" class="text-[#0066cc] hover:underline">Dự đoán xổ số ngày mai</a>
                    </h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 px-4 pb-4">
                    @foreach($predictionOrder as $slug)
                        @php $pred = $latestPredictions[$slug] ?? null; @endphp
                        @if($pred)
                            @php
                                $predDate = $pred->prediction_date;
                                $weekdayLabel = $weekdays[$predDate->dayOfWeek] ?? '';
                                $dateLabel = $weekdayLabel . ', ' . $predDate->format('d/m/Y');
                            @endphp
                            <div>
                                <a href="{{ $pred->url }}" class="block">
                                    @include('predictions.partials.thumbnail-large', [
                                        'thumbSlug' => $slug,
                                        'thumbDateLine' => $dateLabel,
                                    ])
                                </a>
                                <div class="mt-2">
                                    <h3 class="font-bold text-base">
                                        <a href="{{ $pred->url }}" class="text-[#0066cc] hover:underline">
                                            DỰ ĐOÁN {{ strtoupper($slug) }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-700 mt-1">
                                        Dự đoán {{ strtoupper($slug) }} {{ $pred->formatted_date }}, soi cầu Xổ Số {{ $pred->region_name }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Quick Region Links Tabs -->
            <div class="grid md:grid-cols-3 gap-3 mb-4">
                <!-- KQXSMB Tab -->
                <div class="sidebar-section !mb-0 rounded overflow-hidden">
                    <div class="sidebar-header text-sm">
                        <a href="/xsmb" class="text-white hover:underline">KQXSMB - Xổ số miền Bắc</a>
                    </div>
                    <ul class="grid grid-cols-2 gap-x-2 gap-y-1 p-3 text-xs">
                        @foreach($northProvinces->take(6) as $province)
                            <li>
                                <a href="{{ route('province.detail', ['region' => 'xsmb', 'slug' => $province->slug]) }}"
                                   class="text-[#0066cc] hover:text-[#ff6600] hover:underline">
                                    {{ $province->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- KQXSMT Tab -->
                <div class="sidebar-section !mb-0 rounded overflow-hidden">
                    <div class="sidebar-header text-sm">
                        <a href="/xsmt" class="text-white hover:underline">KQXSMT - Xổ số miền Trung</a>
                    </div>
                    <ul class="grid grid-cols-2 gap-x-2 gap-y-1 p-3 text-xs">
                        @foreach($centralProvinces->take(6) as $province)
                            <li>
                                <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $province->slug]) }}"
                                   class="text-[#0066cc] hover:text-[#ff6600] hover:underline">
                                    {{ $province->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- KQXSMN Tab -->
                <div class="sidebar-section !mb-0 rounded overflow-hidden">
                    <div class="sidebar-header text-sm">
                        <a href="/xsmn" class="text-white hover:underline">KQXSMN - Xổ số miền Nam</a>
                    </div>
                    <ul class="grid grid-cols-2 gap-x-2 gap-y-1 p-3 text-xs">
                        @foreach($southProvinces->take(6) as $province)
                            <li>
                                <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $province->slug]) }}"
                                   class="text-[#0066cc] hover:text-[#ff6600] hover:underline">
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
                @php
                    $drawDate = \Carbon\Carbon::parse($result->draw_date);
                    $formattedDate = $drawDate->format('d/m/Y');
                    $dayOfWeek = $drawDate->isoFormat('dddd');
                    $provinceName = $result->province->name ?? 'Hà Nội';

                    // Extract all last-2-digit numbers for lô tô
                    $allNumbers = [];
                    foreach(['prize_special', 'prize_1', 'prize_2', 'prize_3', 'prize_4', 'prize_5', 'prize_6', 'prize_7'] as $prize) {
                        if($result->$prize) {
                            $numbers = explode(',', $result->$prize);
                            foreach($numbers as $num) {
                                $num = trim($num);
                                if(strlen($num) >= 2) {
                                    $allNumbers[] = substr($num, -2);
                                }
                            }
                        }
                    }

                    // Group by head digit (first digit of last 2)
                    $lotoByHead = array_fill(0, 10, []);
                    foreach($allNumbers as $num) {
                        $lotoByHead[intval($num[0])][] = $num;
                    }

                    // Group by tail digit (last digit of last 2)
                    $lotoByTail = array_fill(0, 10, []);
                    foreach($allNumbers as $num) {
                        $lotoByTail[intval($num[1])][] = $num;
                    }
                @endphp
                <div class="mb-6 border border-gray-300 bg-white rounded overflow-hidden">

                    <!-- Header -->
                    <div class="bg-[#ffe89f] px-4 py-2 border-b border-b-gray-300">
                        <h2 class="font-bold text-gray-800 text-base">
                            KQXSMB - SXMB - Kết quả xổ số Miền Bắc
                        </h2>
                        <div class="text-sm mt-1">
                            <a href="{{ route('xsmb') }}" class="text-blue-700 hover:underline">XSMB</a>
                            <span class="mx-1 text-gray-500">&raquo;</span>
                            <a href="{{ route('xsmb') }}" class="text-blue-700 hover:underline">XSMB {{ $dayOfWeek }}</a>
                            <span class="mx-1 text-gray-500">&raquo;</span>
                            <a href="{{ route('xsmb') }}" class="text-blue-700 hover:underline">XSMB {{ $formattedDate }}</a>
                        </div>
                    </div>

                    <!-- Prize Table -->
                    <table class="w-full border-collapse text-center">
                        <tbody>
                            <!-- Table header: Giải | Province -->
                            <tr class="border-b border-gray-300 bg-gray-100">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 w-20 text-sm">Giải</td>
                                <td class="px-3 py-2 font-bold text-blue-700 text-sm">{{ $provinceName }}</td>
                            </tr>
                            <!-- Mã ĐB -->
                            @if($result->turn_num)
                            <tr class="border-b border-gray-200 bg-white">
                                <td class="px-3 py-1.5 font-semibold text-gray-600 border-r border-gray-300 text-sm">Mã ĐB</td>
                                <td class="px-3 py-1.5 font-bold text-orange-600 text-sm">{{ $result->turn_num }}</td>
                            </tr>
                            @endif
                            <!-- G.ĐB - Special Prize -->
                            <tr class="border-b border-gray-200 bg-red-50/40">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.ĐB</td>
                                <td class="px-3 py-2">
                                    <span class="text-4xl font-bold text-red-600">{{ $result->prize_special }}</span>
                                </td>
                            </tr>
                            <!-- G.1 -->
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.1</td>
                                <td class="px-3 py-2">
                                    <span class="text-2xl font-bold">{{ $result->prize_1 }}</span>
                                </td>
                            </tr>
                            <!-- G.2 - 2 numbers -->
                            @if($result->prize_2)
                            <tr class="border-b border-gray-200 bg-white">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.2</td>
                                <td class="px-3 py-2">
                                    <div class="grid grid-cols-2 gap-1">
                                        @foreach(explode(',', $result->prize_2) as $num)
                                            <span class="text-xl font-bold">{{ trim($num) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            <!-- G.3 - 6 numbers in 3-col × 2-row -->
                            @if($result->prize_3)
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.3</td>
                                <td class="px-3 py-2">
                                    <div class="grid grid-cols-3 gap-1">
                                        @foreach(explode(',', $result->prize_3) as $num)
                                            <span class="text-lg font-bold">{{ trim($num) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            <!-- G.4 - 4 numbers -->
                            @if($result->prize_4)
                            <tr class="border-b border-gray-200 bg-white">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.4</td>
                                <td class="px-3 py-2">
                                    <div class="grid grid-cols-4 gap-1">
                                        @foreach(explode(',', $result->prize_4) as $num)
                                            <span class="text-lg font-bold">{{ trim($num) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            <!-- G.5 - 6 numbers in 3-col × 2-row -->
                            @if($result->prize_5)
                            <tr class="border-b border-gray-200 bg-gray-50/50">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.5</td>
                                <td class="px-3 py-2">
                                    <div class="grid grid-cols-3 gap-1">
                                        @foreach(explode(',', $result->prize_5) as $num)
                                            <span class="text-base font-bold">{{ trim($num) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            <!-- G.6 - 3 numbers -->
                            @if($result->prize_6)
                            <tr class="border-b border-gray-200 bg-white">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.6</td>
                                <td class="px-3 py-2">
                                    <div class="grid grid-cols-3 gap-1">
                                        @foreach(explode(',', $result->prize_6) as $num)
                                            <span class="text-base font-bold">{{ trim($num) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                            <!-- G.7 - 4 numbers in RED -->
                            @if($result->prize_7)
                            <tr class="bg-gray-50/50">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm">G.7</td>
                                <td class="px-3 py-2">
                                    <div class="grid grid-cols-4 gap-1">
                                        @foreach(explode(',', $result->prize_7) as $num)
                                            <span class="text-2xl font-bold text-red-600">{{ trim($num) }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>

                    <!-- Lô tô Table (4 columns) -->
                    <div class="border-t border-gray-300">
                        <div class="bg-gray-100 px-4 py-2 font-bold text-sm border-b border-gray-300">
                            Lô tô {{ $provinceName }} - {{ $formattedDate }}
                        </div>
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-gray-300 bg-gray-50">
                                    <th class="px-2 py-2 font-bold text-gray-700 border-r border-gray-300 w-12">Đầu</th>
                                    <th class="px-2 py-2 font-bold text-gray-700 border-r border-gray-300">Lô tô</th>
                                    <th class="px-2 py-2 font-bold text-gray-700 border-r border-gray-300">Lô tô</th>
                                    <th class="px-2 py-2 font-bold text-gray-700 w-12">Đuôi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i <= 9; $i++)
                                <tr class="border-b border-gray-200 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                    <td class="px-2 py-1.5 text-center font-bold text-teal-600 border-r border-gray-300">{{ $i }}</td>
                                    <td class="px-2 py-1.5 border-r border-gray-300">
                                        @if(count($lotoByHead[$i]) > 0)
                                            {{ implode(', ', $lotoByHead[$i]) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1.5 border-r border-gray-300">
                                        @if(count($lotoByTail[$i]) > 0)
                                            {{ implode(', ', $lotoByTail[$i]) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-1.5 text-center font-bold text-teal-600">{{ $i }}</td>
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                </div>
                @endforeach

                <!-- Tiện ích Miền Bắc -->
                <div class="mb-6 border border-gray-300 bg-white rounded overflow-hidden">
                    <div class="bg-[#ffe89f] px-4 py-2 border-b border-gray-300">
                        <h3 class="font-bold text-gray-800 text-base">Tiện ích Miền Bắc</h3>
                    </div>
                    <div class="px-4 py-3">
                        <ul class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 text-sm list-disc list-inside">
                            <li><a href="{{ route('xsmb') }}" class="text-blue-800 hover:underline">Kết quả XSMB</a></li>
                            <li><a href="{{ route('prediction.xsmb.index') }}" class="text-blue-800 hover:underline">Dự đoán XSMB</a></li>
                            <li><a href="{{ route('trial.xsmb') }}" class="text-blue-800 hover:underline">Quay thử XSMB</a></li>
                            <li><a href="{{ route('statistics.quick') }}" class="text-blue-800 hover:underline">Thống kê XSMB</a></li>
                            <li><a href="{{ route('xsmb.live') }}" class="text-blue-800 hover:underline">Trực tiếp XSMB</a></li>
                            <li><a href="{{ route('statistics.overdue', ['province_id' => 1, 'min_gap' => 0, 'max_gap' => 100]) }}" class="text-blue-800 hover:underline">Lô gan XSMB</a></li>
                            <li><a href="{{ route('statistics.head-tail', ['province_id' => 1, 'period' => 30]) }}" class="text-blue-800 hover:underline">Đầu đuôi XSMB</a></li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- XSMN Results Section -->
            @if($southResults->count() > 0)
            @php
                $xsmnDate = \Carbon\Carbon::parse($southResults->first()->draw_date);
                $xsmnFormattedDate = $xsmnDate->format('d/m/Y');
                $xsmnDayOfWeek = $xsmnDate->isoFormat('dddd');

                $xsmnPrizes = [
                    ['label' => 'G.8',  'field' => 'prize_8',       'class' => 'text-2xl font-bold text-red-600'],
                    ['label' => 'G.7',  'field' => 'prize_7',       'class' => 'text-xl font-bold'],
                    ['label' => 'G.6',  'field' => 'prize_6',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.5',  'field' => 'prize_5',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.4',  'field' => 'prize_4',       'class' => 'text-base font-bold'],
                    ['label' => 'G.3',  'field' => 'prize_3',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.2',  'field' => 'prize_2',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.1',  'field' => 'prize_1',       'class' => 'text-xl font-bold'],
                    ['label' => 'G.ĐB', 'field' => 'prize_special', 'class' => 'text-2xl font-bold text-red-600'],
                ];

                // Lô tô per province
                $xsmnLoto = [];
                foreach($southResults as $res) {
                    $allNums = [];
                    foreach(['prize_special','prize_1','prize_2','prize_3','prize_4','prize_5','prize_6','prize_7','prize_8'] as $p) {
                        if($res->$p) {
                            foreach(explode(',', $res->$p) as $num) {
                                $num = trim($num);
                                if(strlen($num) >= 2) {
                                    $allNums[] = substr($num, -2);
                                }
                            }
                        }
                    }
                    $byHead = array_fill(0, 10, []);
                    foreach($allNums as $n) {
                        $byHead[intval($n[0])][] = $n;
                    }
                    $xsmnLoto[$res->province->id] = $byHead;
                }
            @endphp
            <div class="mb-6 border border-gray-300 bg-white rounded overflow-hidden">

                <!-- Header -->
                <div class="bg-[#ffe89f] px-4 py-2 border-b border-gray-300">
                    <h2 class="font-bold text-gray-800 text-base">
                        KQXSMN - SXMN - Kết quả xổ số Miền Nam
                    </h2>
                    <div class="text-sm mt-1">
                        <a href="{{ route('xsmn') }}" class="text-blue-700 hover:underline">XSMN</a>
                        <span class="mx-1 text-gray-500">&raquo;</span>
                        <a href="{{ route('xsmn') }}" class="text-blue-700 hover:underline">XSMN {{ $xsmnDayOfWeek }}</a>
                        <span class="mx-1 text-gray-500">&raquo;</span>
                        <a href="{{ route('xsmn') }}" class="text-blue-700 hover:underline">XSMN {{ $xsmnFormattedDate }}</a>
                    </div>
                </div>

                <!-- Prize Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm text-center">
                        <tbody>
                            <!-- Header row -->
                            <tr class="border-b border-gray-300 bg-[#E8EAF6]">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 w-16 text-sm">Giải</td>
                                @foreach($southResults as $result)
                                    <td class="px-3 py-2 font-bold text-sm border-r border-gray-300">
                                        <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $result->province->slug]) }}" class="text-blue-700 underline hover:text-blue-900">{{ $result->province->name }}</a>
                                    </td>
                                @endforeach
                            </tr>

                            @foreach($xsmnPrizes as $pi => $prize)
                                <tr class="border-b border-gray-200 {{ $pi % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                    <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm bg-gray-50">{{ $prize['label'] }}</td>
                                    @foreach($southResults as $result)
                                        <td class="px-3 py-1.5 border-r border-gray-300">
                                            @if(isset($result->{$prize['field']}) && $result->{$prize['field']})
                                                <div class="flex flex-col items-center gap-0.5">
                                                    @foreach(explode(',', $result->{$prize['field']}) as $num)
                                                        <span class="{{ $prize['class'] }}">{{ trim($num) }}</span>
                                                    @endforeach
                                                </div>
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

                <!-- Lô tô Table -->
                <div class="border-t border-gray-300">
                    <div class="bg-gray-100 px-4 py-2 font-bold text-sm border-b border-gray-300">
                        Lô tô Miền Nam - {{ $xsmnFormattedDate }}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-gray-300 bg-gray-50">
                                    <th class="px-2 py-2 font-bold text-gray-700 border-r border-gray-300 w-12">Đầu</th>
                                    @foreach($southResults as $result)
                                        <th class="px-2 py-2 font-bold border-r border-gray-300">
                                            <a href="{{ route('province.detail', ['region' => 'xsmn', 'slug' => $result->province->slug]) }}" class="text-blue-700 underline hover:text-blue-900">{{ $result->province->name }}</a>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i <= 9; $i++)
                                <tr class="border-b border-gray-200 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                    <td class="px-2 py-1.5 text-center font-bold text-teal-600 border-r border-gray-300">{{ $i }}</td>
                                    @foreach($southResults as $result)
                                        <td class="px-2 py-1.5 border-r border-gray-300">
                                            @if(count($xsmnLoto[$result->province->id][$i]) > 0)
                                                {{ implode(', ', $xsmnLoto[$result->province->id][$i]) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

                <!-- Tiện ích Miền Nam -->
                <div class="mb-6 border border-gray-300 bg-white rounded overflow-hidden">
                    <div class="bg-[#ffe89f] px-4 py-2 border-b border-gray-300">
                        <h3 class="font-bold text-gray-800 text-base">Tiện ích Miền Nam</h3>
                    </div>
                    <div class="px-4 py-3">
                        <ul class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 text-sm list-disc list-inside">
                            <li><a href="{{ route('xsmn') }}" class="text-blue-800 hover:underline">Kết quả XSMN</a></li>
                            <li><a href="{{ route('prediction.xsmn.index') }}" class="text-blue-800 hover:underline">Dự đoán XSMN</a></li>
                            <li><a href="{{ route('trial.xsmn') }}" class="text-blue-800 hover:underline">Quay thử XSMN</a></li>
                            <li><a href="{{ route('statistics.frequency') }}" class="text-blue-800 hover:underline">Thống kê XSMN</a></li>
                            <li><a href="{{ route('xsmn.live') }}" class="text-blue-800 hover:underline">Trực tiếp XSMN</a></li>
                            <li><a href="{{ route('statistics.overdue') }}" class="text-blue-800 hover:underline">Lô gan XSMN</a></li>
                            <li><a href="{{ route('statistics.head-tail') }}" class="text-blue-800 hover:underline">Đầu đuôi XSMN</a></li>
                            <li><a href="{{ route('statistics.frequency') }}" class="text-blue-800 hover:underline">Tần suất XSMN</a></li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- XSMT Results Section -->
            @if($centralResults->count() > 0)
            @php
                $xsmtDate = \Carbon\Carbon::parse($centralResults->first()->draw_date);
                $xsmtFormattedDate = $xsmtDate->format('d/m/Y');
                $xsmtDayOfWeek = $xsmtDate->isoFormat('dddd');

                $xsmtPrizes = [
                    ['label' => 'G.8',  'field' => 'prize_8',       'class' => 'text-2xl font-bold text-red-600'],
                    ['label' => 'G.7',  'field' => 'prize_7',       'class' => 'text-xl font-bold'],
                    ['label' => 'G.6',  'field' => 'prize_6',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.5',  'field' => 'prize_5',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.4',  'field' => 'prize_4',       'class' => 'text-base font-bold'],
                    ['label' => 'G.3',  'field' => 'prize_3',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.2',  'field' => 'prize_2',       'class' => 'text-lg font-bold'],
                    ['label' => 'G.1',  'field' => 'prize_1',       'class' => 'text-xl font-bold'],
                    ['label' => 'G.ĐB', 'field' => 'prize_special', 'class' => 'text-2xl font-bold text-red-600'],
                ];

                // Lô tô per province
                $xsmtLoto = [];
                foreach($centralResults as $res) {
                    $allNums = [];
                    foreach(['prize_special','prize_1','prize_2','prize_3','prize_4','prize_5','prize_6','prize_7','prize_8'] as $p) {
                        if($res->$p) {
                            foreach(explode(',', $res->$p) as $num) {
                                $num = trim($num);
                                if(strlen($num) >= 2) {
                                    $allNums[] = substr($num, -2);
                                }
                            }
                        }
                    }
                    $byHead = array_fill(0, 10, []);
                    foreach($allNums as $n) {
                        $byHead[intval($n[0])][] = $n;
                    }
                    $xsmtLoto[$res->province->id] = $byHead;
                }
            @endphp
            <div class="mb-6 border border-gray-300 bg-white rounded overflow-hidden">

                <!-- Header -->
                <div class="bg-[#ffe89f] px-4 py-2 border-b border-gray-300">
                    <h2 class="font-bold text-gray-800 text-base">
                        KQXSMT - SXMT - Kết quả xổ số Miền Trung
                    </h2>
                    <div class="text-sm mt-1">
                        <a href="{{ route('xsmt') }}" class="text-blue-700 hover:underline">XSMT</a>
                        <span class="mx-1 text-gray-500">&raquo;</span>
                        <a href="{{ route('xsmt') }}" class="text-blue-700 hover:underline">XSMT {{ $xsmtDayOfWeek }}</a>
                        <span class="mx-1 text-gray-500">&raquo;</span>
                        <a href="{{ route('xsmt') }}" class="text-blue-700 hover:underline">XSMT {{ $xsmtFormattedDate }}</a>
                    </div>
                </div>

                <!-- Prize Table -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm text-center">
                        <tbody>
                            <!-- Header row -->
                            <tr class="border-b border-gray-300 bg-[#E8EAF6]">
                                <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 w-16 text-sm">Giải</td>
                                @foreach($centralResults as $result)
                                    <td class="px-3 py-2 font-bold text-sm border-r border-gray-300">
                                        <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $result->province->slug]) }}" class="text-blue-700 underline hover:text-blue-900">{{ $result->province->name }}</a>
                                    </td>
                                @endforeach
                            </tr>

                            @foreach($xsmtPrizes as $pi => $prize)
                                <tr class="border-b border-gray-200 {{ $pi % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                    <td class="px-3 py-2 font-bold text-gray-700 border-r border-gray-300 text-sm bg-gray-50">{{ $prize['label'] }}</td>
                                    @foreach($centralResults as $result)
                                        <td class="px-3 py-1.5 border-r border-gray-300">
                                            @if(isset($result->{$prize['field']}) && $result->{$prize['field']})
                                                <div class="flex flex-col items-center gap-0.5">
                                                    @foreach(explode(',', $result->{$prize['field']}) as $num)
                                                        <span class="{{ $prize['class'] }}">{{ trim($num) }}</span>
                                                    @endforeach
                                                </div>
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

                <!-- Lô tô Table -->
                <div class="border-t border-gray-300">
                    <div class="bg-gray-100 px-4 py-2 font-bold text-sm border-b border-gray-300">
                        Lô tô Miền Trung - {{ $xsmtFormattedDate }}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-sm">
                            <thead>
                                <tr class="border-b border-gray-300 bg-gray-50">
                                    <th class="px-2 py-2 font-bold text-gray-700 border-r border-gray-300 w-12">Đầu</th>
                                    @foreach($centralResults as $result)
                                        <th class="px-2 py-2 font-bold border-r border-gray-300">
                                            <a href="{{ route('province.detail', ['region' => 'xsmt', 'slug' => $result->province->slug]) }}" class="text-blue-700 underline hover:text-blue-900">{{ $result->province->name }}</a>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @for($i = 0; $i <= 9; $i++)
                                <tr class="border-b border-gray-200 {{ $i % 2 === 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                    <td class="px-2 py-1.5 text-center font-bold text-teal-600 border-r border-gray-300">{{ $i }}</td>
                                    @foreach($centralResults as $result)
                                        <td class="px-2 py-1.5 border-r border-gray-300">
                                            @if(count($xsmtLoto[$result->province->id][$i]) > 0)
                                                {{ implode(', ', $xsmtLoto[$result->province->id][$i]) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

                <!-- Tiện ích Miền Trung -->
                <div class="mb-6 border border-gray-300 bg-white rounded overflow-hidden">
                    <div class="bg-[#ffe89f] px-4 py-2 border-b border-gray-300">
                        <h3 class="font-bold text-gray-800 text-base">Tiện ích Miền Trung</h3>
                    </div>
                    <div class="px-4 py-3">
                        <ul class="grid grid-cols-2 md:grid-cols-4 gap-x-4 gap-y-2 text-sm list-disc list-inside">
                            <li><a href="{{ route('xsmt') }}" class="text-blue-800 hover:underline">Kết quả XSMT</a></li>
                            <li><a href="{{ route('prediction.xsmt.index') }}" class="text-blue-800 hover:underline">Dự đoán XSMT</a></li>
                            <li><a href="{{ route('trial.xsmt') }}" class="text-blue-800 hover:underline">Quay thử XSMT</a></li>
                            <li><a href="{{ route('statistics.frequency') }}" class="text-blue-800 hover:underline">Thống kê XSMT</a></li>
                            <li><a href="{{ route('xsmt.live') }}" class="text-blue-800 hover:underline">Trực tiếp XSMT</a></li>
                            <li><a href="{{ route('statistics.overdue') }}" class="text-blue-800 hover:underline">Lô gan XSMT</a></li>
                            <li><a href="{{ route('statistics.head-tail') }}" class="text-blue-800 hover:underline">Đầu đuôi XSMT</a></li>
                            <li><a href="{{ route('statistics.frequency') }}" class="text-blue-800 hover:underline">Tần suất XSMT</a></li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Vietlott Mega 6/45 & Power 6/55 -->
            @if($vietlottResults['mega645'] || $vietlottResults['power655'])
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @if($vietlottResults['mega645'])
                    <x-vietlott.mega-result-card :result="$vietlottResults['mega645']" />
                @endif
                @if($vietlottResults['power655'])
                    <x-vietlott.power-result-card :result="$vietlottResults['power655']" />
                @endif
            </div>
            @endif

            <!-- Max 3D & Max 3D Pro Blocks -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Max 3D -->
                <a href="{{ route('vietlott.max3d') }}" class="block rounded-lg overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-[#C44D8B] px-4 py-3">
                        <img class="w-1/2 h-2/3 mx-auto" src="{{ asset('images/vietlott-max-3d-logo-white.png') }}" alt="Max 3D"/>
                    </div>
                    <div class="bg-white p-4">
                        <div class="font-semibold text-gray-800 text-base">Xổ Số Max 3D</div>
                        <div class="text-gray-500 text-sm mt-1">Thứ 2 – Thứ 4 – Thứ 6</div>
                    </div>
                </a>
                <!-- Max 3D Pro -->
                <a href="{{ route('vietlott.max3dpro') }}" class="block rounded-lg overflow-hidden border border-gray-200 shadow-sm hover:shadow-md transition-shadow">
                    <div class="bg-[#6A4C93] px-4 py-3">
                        <img class="w-1/2 h-2/3 mx-auto" src="{{ asset('images/vietlott-max-3d-pro-logo-white.png') }}" alt="Max 3D Pro"/>
                    </div>
                    <div class="bg-white p-4">
                        <div class="font-semibold text-gray-800 text-base">Xổ Số Max 3D Pro</div>
                        <div class="text-gray-500 text-sm mt-1">Thứ 3 – Thứ 5 – Thứ 7</div>
                    </div>
                </a>
            </div>

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
