@php
    $formattedDate = \Carbon\Carbon::parse($date)->format('d/m/Y');
    $perProvince = $analysisData['per_province'] ?? [];
@endphp

@foreach($provinces as $province)
    @php
        $provinceSlug = $province->slug;
        $data = $perProvince[$provinceSlug] ?? [];

        $bachThu = $data['bach_thu'] ?? null;
        $latLienTuc = $data['lat_lien_tuc'] ?? null;
        $loGan = $data['lo_gan'] ?? null;
        $pascal = $data['pascal'] ?? [];
        $loKep = $data['lo_kep'] ?? null;
        $lotoHayVe = $data['loto_hay_ve'] ?? [];
    @endphp

    <div class="my-6 bg-white border border-gray-300 rounded-lg overflow-hidden">
        <div class="bg-gray-50 border-b border-gray-300 px-4 py-3">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <span class="text-green-600">&#10004;</span>
                Nhận định xổ số {{ $province->name }} {{ $formattedDate }}
            </h3>
        </div>

        <div class="p-4">
            <table class="w-full border-collapse">
                <tbody>
                    {{-- Bạch thủ --}}
                    <tr class="border-b border-gray-200">
                        <td class="py-2 pr-3 text-gray-700 w-[35%]">
                            <span class="text-amber-500">&#9733;</span> Bạch thủ
                        </td>
                        <td class="py-2">
                            @if($bachThu)
                                <span class="font-bold text-red-600">{{ $bachThu }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Lật liên tục --}}
                    <tr class="border-b border-gray-200">
                        <td class="py-2 pr-3 text-gray-700">
                            <span class="text-amber-500">&#9733;</span> Lật liên tục
                        </td>
                        <td class="py-2">
                            @if($latLienTuc)
                                <span class="font-bold text-red-600">{{ $latLienTuc }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Lô gan --}}
                    <tr class="border-b border-gray-200">
                        <td class="py-2 pr-3 text-gray-700">
                            <span class="text-amber-500">&#9733;</span> Lô gan
                        </td>
                        <td class="py-2">
                            @if($loGan)
                                với lô gan <span class="font-bold text-red-600">{{ $loGan['number'] ?? '-' }}</span>
                                lâu ra nhất ({{ $loGan['days'] ?? 0 }} ngày)
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Pascal --}}
                    <tr class="border-b border-gray-200">
                        <td class="py-2 pr-3 text-gray-700">
                            <span class="text-amber-500">&#9733;</span> Pascal
                        </td>
                        <td class="py-2">
                            @if(!empty($pascal))
                                <span class="font-bold text-red-600">{{ implode(' - ', $pascal) }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Lô kép --}}
                    <tr class="border-b border-gray-200">
                        <td class="py-2 pr-3 text-gray-700">
                            <span class="text-amber-500">&#9733;</span> Lô kép
                        </td>
                        <td class="py-2">
                            @if($loKep)
                                <span class="font-bold text-red-600">{{ $loKep }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>

                    {{-- Lô tô hay về --}}
                    <tr>
                        <td class="py-2 pr-3 text-gray-700">
                            <span class="text-amber-500">&#9733;</span> Lô tô hay về
                        </td>
                        <td class="py-2">
                            @if(!empty($lotoHayVe))
                                <span class="font-bold text-red-600">{{ implode(' - ', $lotoHayVe) }}</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endforeach
