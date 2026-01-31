@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
    $perProvince = $predictionsData['per_province'] ?? [];
@endphp

@foreach($provinces as $province)
    @php
        $provinceSlug = $province->slug;
        $data = $perProvince[$provinceSlug] ?? [];
        $giaiTam = $data['giai_tam'] ?? null;
        $dacBietHead = $data['dac_biet_head'] ?? null;
        $dacBietTail = $data['dac_biet_tail'] ?? null;
        $baoLo2 = $data['bao_lo_2'] ?? [];
    @endphp

    <div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="text-green-600">&#10004;</span>
                Chốt số thần tài VIP, lô dự đoán {{ $province->name }} {{ $formattedDate }}
            </h3>
        </div>

        <div class="p-4 space-y-3">
            {{-- Giải tám --}}
            <div class="flex items-start gap-2">
                <span class="text-amber-500 mt-0.5">&#9733;</span>
                <div>
                    <span class="text-gray-700">Giải tám: </span>
                    @if($giaiTam)
                        <span class="font-bold text-red-600 text-lg">{{ $giaiTam }}</span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>

            {{-- Đặc biệt: đầu, đuôi --}}
            <div class="flex items-start gap-2">
                <span class="text-amber-500 mt-0.5">&#9733;</span>
                <div>
                    <span class="text-gray-700">Đặc biệt: đầu, đuôi: </span>
                    @if($dacBietHead || $dacBietTail)
                        <span class="font-bold text-red-600 text-lg">{{ $dacBietHead ?? '-' }} - {{ $dacBietTail ?? '-' }}</span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>

            {{-- Bao lô 2 số --}}
            <div class="flex items-start gap-2">
                <span class="text-amber-500 mt-0.5">&#9733;</span>
                <div>
                    <span class="text-gray-700">Bao lô 2 số: </span>
                    @if(!empty($baoLo2))
                        <span class="font-bold text-red-600 text-lg">{{ implode(' - ', $baoLo2) }}</span>
                    @else
                        <span class="text-gray-500">-</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endforeach
