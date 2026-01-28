@props([
    'categories' => collect([]),
    'popularArticles' => collect([]),
])

<div class="space-y-4 w-full lg:w-[280px]">
    {{-- Categories Section --}}
    @if($categories->count() > 0)
        <div class="sidebar-section">
            <div class="sidebar-header">{{ __('article.frontend.categories') }}</div>
            <ul class="text-sm">
                @foreach($categories as $category)
                    <li class="{{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <a href="{{ route('news.category', $category->slug) }}"
                           class="flex items-center justify-between py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                            <span>{{ $category->name }}</span>
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                {{ $category->articles_count ?? 0 }}
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Popular Articles Section --}}
    @if($popularArticles->count() > 0)
        <div class="sidebar-section">
            <div class="sidebar-header">{{ __('article.frontend.popular_news') }}</div>
            <ul class="text-sm">
                @foreach($popularArticles as $article)
                    <li class="{{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <a href="{{ route('news.show', $article->slug) }}"
                           class="flex gap-3 py-3 px-3 hover:bg-gray-50 transition-colors group">
                            @if($article->featured_image)
                                <img src="{{ Storage::url($article->featured_image) }}"
                                     alt="{{ $article->title }}"
                                     class="w-16 h-12 object-cover rounded flex-shrink-0">
                            @else
                                <div class="w-16 h-12 bg-gray-200 rounded flex-shrink-0 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[#0066cc] group-hover:text-[#ff6600] font-medium line-clamp-2 text-sm leading-tight">
                                    {{ $article->title }}
                                </h4>
                                <div class="flex items-center text-xs text-gray-500 mt-1 gap-2">
                                    <span>{{ $article->published_at->format('d/m') }}</span>
                                    <span class="flex items-center gap-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        {{ number_format($article->view_count) }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Back to Homepage --}}
    <div class="sidebar-section">
        <div class="sidebar-header">Liên kết nhanh</div>
        <ul class="text-sm">
            <li class="border-b border-gray-200">
                <a href="{{ route('home') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    Trang chủ
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('xsmb') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMB
                </a>
            </li>
            <li class="border-b border-gray-200">
                <a href="{{ route('xsmt') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMT
                </a>
            </li>
            <li>
                <a href="{{ route('xsmn') }}"
                   class="block py-2 px-3 text-[#0066cc] hover:text-[#ff6600] hover:bg-gray-50 transition-colors">
                    XSMN
                </a>
            </li>
        </ul>
    </div>
</div>
