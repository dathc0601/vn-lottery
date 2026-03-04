<div id="prediction-list">
    @foreach($predictions as $prediction)
        @include('predictions.partials.hub.prediction-card-item', ['prediction' => $prediction])
    @endforeach
</div>

@if($predictions->count() < $totalCount)
    <div id="load-more-wrapper" class="text-center py-4">
        <button id="load-more-btn"
                type="button"
                data-offset="{{ $predictions->count() }}"
                data-url="{{ route('api.predictions.loadMore') }}"
                class="inline-flex items-center gap-2 px-6 py-2 bg-[#cc0000] text-white text-sm font-medium hover:bg-[#a00000] transition-colors">
            Xem thêm dự đoán
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div id="load-more-spinner" class="hidden">
            <svg class="animate-spin h-6 w-6 text-[#cc0000] mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('load-more-btn');
    if (!btn) return;

    const list = document.getElementById('prediction-list');
    const wrapper = document.getElementById('load-more-wrapper');
    const spinner = document.getElementById('load-more-spinner');

    btn.addEventListener('click', function () {
        const offset = parseInt(btn.dataset.offset, 10);
        const url = btn.dataset.url + '?offset=' + offset;

        btn.classList.add('hidden');
        spinner.classList.remove('hidden');

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            list.insertAdjacentHTML('beforeend', data.html);
            btn.dataset.offset = data.loaded;

            spinner.classList.add('hidden');

            if (data.hasMore) {
                btn.classList.remove('hidden');
            } else {
                wrapper.remove();
            }
        })
        .catch(function () {
            spinner.classList.add('hidden');
            btn.classList.remove('hidden');
        });
    });
});
</script>
@endpush
