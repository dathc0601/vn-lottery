<div class="sidebar-section">
    <div class="sidebar-header-dark">Dò Vé Số</div>
    <div class="p-3">
        <form action="{{ route('ticket.verify') }}" method="POST" class="space-y-2">
            @csrf
            <div>
                <label class="block text-sm text-gray-700 mb-1">Ngày xổ số:</label>
                <input type="date" name="date" value="{{ date('Y-m-d') }}"
                       class="w-full px-3 py-2 border border-gray-300 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Chọn miền/tỉnh:</label>
                <select name="province" class="w-full px-3 py-2 border border-gray-300 text-sm">
                    <option value="">-- Chọn miền/tỉnh --</option>
                    <optgroup label="Miền Bắc">
                        @foreach($northProvinces as $province)
                            <option value="{{ $province->slug }}">{{ $province->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Miền Trung">
                        @foreach($centralProvinces as $province)
                            <option value="{{ $province->slug }}">{{ $province->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Miền Nam">
                        @foreach($southProvinces as $province)
                            <option value="{{ $province->slug }}">{{ $province->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Nhập số vé:</label>
                <textarea name="tickets" rows="3" placeholder="Nhập các số vé cần dò, mỗi số một dòng"
                          class="w-full px-3 py-2 border border-gray-300 text-sm resize-y"></textarea>
            </div>
            <button type="submit"
                    class="w-full bg-[#8B2500] hover:bg-[#a03000] text-white px-4 py-2 font-bold text-sm transition-colors">
                Dò vé số
            </button>
        </form>
    </div>
</div>
