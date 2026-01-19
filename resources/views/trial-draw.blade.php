@extends('layouts.app')

@section('title', 'Quay Thử Xổ Số - Mô phỏng kết quả xổ số')

@section('page-content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-xl p-6 shadow-lg">
        <h1 class="text-3xl font-bold mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
            </svg>
            Quay Thử Xổ Số
        </h1>
        <p class="text-green-100">Mô phỏng kết quả xổ số với các số ngẫu nhiên</p>
    </div>

    <!-- Control Panel -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Chọn miền</label>
                <select id="regionSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                    <option value="north">Miền Bắc (XSMB)</option>
                    <option value="central">Miền Trung (XSMT)</option>
                    <option value="south">Miền Nam (XSMN)</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tỉnh</label>
                <select id="provinceSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#4a7c2c] focus:border-transparent">
                    @foreach($provinces->groupBy('region') as $regionName => $regionProvinces)
                        <optgroup label="{{ $regionName == 'north' ? 'Miền Bắc' : ($regionName == 'central' ? 'Miền Trung' : 'Miền Nam') }}" data-region="{{ $regionName }}">
                            @foreach($regionProvinces as $province)
                                <option value="{{ $province->id }}" data-region="{{ $regionName }}" data-name="{{ $province->name }}">
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>

        <button id="generateBtn" class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-[#2d5016] to-[#4a7c2c] text-white rounded-lg hover:from-[#3a6020] hover:to-[#5a8c3c] transition-all duration-200 font-bold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
            </svg>
            Quay Số Ngẫu Nhiên
        </button>
    </div>

    <!-- Results Display (Hidden until generated) -->
    <div id="resultsContainer" class="hidden">
        <div class="bg-white border-2 border-[#4a7c2c] rounded-xl overflow-hidden shadow-lg">
            <!-- Header -->
            <div class="bg-gradient-to-br from-[#2d5016] to-[#4a7c2c] text-white p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold" id="provinceName">Miền Bắc</h3>
                        <p class="text-sm text-green-100 mt-1">Kết quả mô phỏng - <span id="currentDate"></span></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block bg-white/20 px-3 py-1 rounded-full text-sm">Thử nghiệm</span>
                    </div>
                </div>
            </div>

            <!-- Prize Table -->
            <div class="p-6">
                <table class="result-table w-full">
                    <tbody>
                        <tr class="bg-red-50">
                            <td class="prize-label w-1/4">Giải ĐB</td>
                            <td class="prize-special text-2xl" id="prizeSpecial">------</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Nhất</td>
                            <td class="text-lg font-bold text-blue-700" id="prize1">-----</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Nhì</td>
                            <td class="font-semibold" id="prize2">----- - -----</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Ba</td>
                            <td class="text-sm" id="prize3">----- - -----</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Tư</td>
                            <td class="text-sm" id="prize4">----- - ----- - ----- - -----</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Năm</td>
                            <td class="text-sm" id="prize5">---- - ---- - ---- - ---- - ---- - ----</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Sáu</td>
                            <td class="text-sm" id="prize6">--- - --- - ---</td>
                        </tr>
                        <tr>
                            <td class="prize-label">Giải Bảy</td>
                            <td class="text-sm" id="prize7">--- - --- - --- - ---</td>
                        </tr>
                        <tr id="prize8Row" class="hidden">
                            <td class="prize-label">Giải Tám</td>
                            <td class="text-sm" id="prize8">--</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Action Buttons -->
            <div class="px-6 pb-6 flex gap-3">
                <button id="regenerateBtn" class="flex-1 px-4 py-2 bg-[#4a7c2c] text-white rounded-lg hover:bg-[#5a8c3c] transition-colors font-medium">
                    Quay Lại
                </button>
                <button id="copyBtn" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Sao Chép
                </button>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm text-yellow-800">
                    <p class="font-medium">Lưu ý:</p>
                    <p class="mt-1">Đây chỉ là kết quả mô phỏng ngẫu nhiên, không phải kết quả chính thức. Kết quả chỉ mang tính chất tham khảo và giải trí.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const generateBtn = document.getElementById('generateBtn');
    const regenerateBtn = document.getElementById('regenerateBtn');
    const copyBtn = document.getElementById('copyBtn');
    const resultsContainer = document.getElementById('resultsContainer');
    const regionSelect = document.getElementById('regionSelect');
    const provinceSelect = document.getElementById('provinceSelect');

    // Generate random number with specified digits
    function generateNumber(digits) {
        const min = Math.pow(10, digits - 1);
        const max = Math.pow(10, digits) - 1;
        return String(Math.floor(Math.random() * (max - min + 1)) + min).padStart(digits, '0');
    }

    // Generate lottery results
    function generateResults() {
        const region = regionSelect.value;
        const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
        const provinceName = selectedOption.getAttribute('data-name');

        // Show results container
        resultsContainer.classList.remove('hidden');
        resultsContainer.scrollIntoView({ behavior: 'smooth' });

        // Set province name and date
        document.getElementById('provinceName').textContent = provinceName;
        document.getElementById('currentDate').textContent = new Date().toLocaleDateString('vi-VN');

        // Generate prizes
        document.getElementById('prizeSpecial').textContent = generateNumber(6);
        document.getElementById('prize1').textContent = generateNumber(5);

        // Prize 2: 2 numbers
        document.getElementById('prize2').textContent =
            [generateNumber(5), generateNumber(5)].join(' - ');

        // Prize 3: 6 numbers
        const prize3 = [];
        for (let i = 0; i < 6; i++) prize3.push(generateNumber(5));
        document.getElementById('prize3').textContent = prize3.join(' - ');

        // Prize 4: 4 numbers
        const prize4 = [];
        for (let i = 0; i < 4; i++) prize4.push(generateNumber(5));
        document.getElementById('prize4').textContent = prize4.join(' - ');

        // Prize 5: 6 numbers (4 digits)
        const prize5 = [];
        for (let i = 0; i < 6; i++) prize5.push(generateNumber(4));
        document.getElementById('prize5').textContent = prize5.join(' - ');

        // Prize 6: 3 numbers (4 digits)
        const prize6 = [];
        for (let i = 0; i < 3; i++) prize6.push(generateNumber(4));
        document.getElementById('prize6').textContent = prize6.join(' - ');

        // Prize 7: 4 numbers (3 digits)
        const prize7 = [];
        for (let i = 0; i < 4; i++) prize7.push(generateNumber(3));
        document.getElementById('prize7').textContent = prize7.join(' - ');

        // Prize 8 (only for South region)
        const prize8Row = document.getElementById('prize8Row');
        if (region === 'south') {
            prize8Row.classList.remove('hidden');
            document.getElementById('prize8').textContent = generateNumber(2);
        } else {
            prize8Row.classList.add('hidden');
        }
    }

    // Copy results to clipboard
    function copyResults() {
        const provinceName = document.getElementById('provinceName').textContent;
        const date = document.getElementById('currentDate').textContent;

        let text = `Kết quả mô phỏng - ${provinceName} - ${date}\n\n`;
        text += `Giải ĐB: ${document.getElementById('prizeSpecial').textContent}\n`;
        text += `Giải Nhất: ${document.getElementById('prize1').textContent}\n`;
        text += `Giải Nhì: ${document.getElementById('prize2').textContent}\n`;
        text += `Giải Ba: ${document.getElementById('prize3').textContent}\n`;
        text += `Giải Tư: ${document.getElementById('prize4').textContent}\n`;
        text += `Giải Năm: ${document.getElementById('prize5').textContent}\n`;
        text += `Giải Sáu: ${document.getElementById('prize6').textContent}\n`;
        text += `Giải Bảy: ${document.getElementById('prize7').textContent}\n`;

        if (!document.getElementById('prize8Row').classList.contains('hidden')) {
            text += `Giải Tám: ${document.getElementById('prize8').textContent}\n`;
        }

        navigator.clipboard.writeText(text).then(() => {
            // Show success message
            const originalText = copyBtn.textContent;
            copyBtn.textContent = '✓ Đã sao chép';
            copyBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            copyBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');

            setTimeout(() => {
                copyBtn.textContent = originalText;
                copyBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                copyBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            }, 2000);
        });
    }

    // Filter provinces by region
    function filterProvinces() {
        const selectedRegion = regionSelect.value;
        const options = provinceSelect.querySelectorAll('option');
        const optgroups = provinceSelect.querySelectorAll('optgroup');

        // Hide all optgroups first
        optgroups.forEach(group => {
            group.style.display = 'none';
        });

        // Show only the selected region's optgroup
        optgroups.forEach(group => {
            if (group.getAttribute('data-region') === selectedRegion) {
                group.style.display = '';
                // Select first option in this group
                const firstOption = group.querySelector('option');
                if (firstOption) {
                    firstOption.selected = true;
                }
            }
        });
    }

    // Event listeners
    generateBtn.addEventListener('click', generateResults);
    regenerateBtn.addEventListener('click', generateResults);
    copyBtn.addEventListener('click', copyResults);
    regionSelect.addEventListener('change', filterProvinces);

    // Initialize province filter
    filterProvinces();
});
</script>
@endsection
