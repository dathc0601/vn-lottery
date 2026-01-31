@php
    $headTail = $predictionsData['head_tail'] ?? [];
    $loto2Digit = $predictionsData['loto_2_digit'] ?? [];
    $loto3Digit = $predictionsData['loto_3_digit'] ?? [];
    $vip4Digit = $predictionsData['vip_4_digit'] ?? [];

    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
@endphp

<div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
    <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <span class="text-green-600">✅</span>
            Chốt số Thần Tài Dự đoán KQ {{ strtoupper($regionSlug) }} {{ $formattedDate }}
        </h2>
    </div>

    <div class="p-4 space-y-4">
        {{-- Đầu đuôi giải ĐB --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">☆</span>
            <div>
                <span class="text-gray-700">Đầu đuôi giải đặc biệt: </span>
                @if(!empty($headTail['combined']))
                    <span class="font-bold text-red-600">{{ implode(' - ', $headTail['combined']) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>

        {{-- Loto 2 số hay về --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">☆</span>
            <div>
                <span class="text-gray-700">Loto 2 số hay về: </span>
                @if(!empty($loto2Digit))
                    <span class="font-bold text-red-600">{{ implode(' - ', $loto2Digit) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>

        {{-- Lô tô 3 số - 3 càng đẹp --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">☆</span>
            <div>
                <span class="text-gray-700">Lô tô 3 số - 3 càng đẹp: </span>
                @if(!empty($loto3Digit))
                    <span class="font-bold text-red-600">{{ implode(' - ', $loto3Digit) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>

        {{-- Soi cầu 4 số VIP --}}
        <div class="flex items-start gap-2">
            <span class="text-amber-500 mt-0.5">☆</span>
            <div>
                <span class="text-gray-700">Soi cầu 4 số VIP: </span>
                @if(!empty($vip4Digit))
                    <span class="font-bold text-red-600">{{ implode(' - ', $vip4Digit) }}</span>
                @else
                    <span class="text-gray-500">Chưa có dữ liệu</span>
                @endif
            </div>
        </div>
    </div>
</div>
