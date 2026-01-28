@props([
    'article',
    'featured' => false,
])

@php
    $cardClass = $featured
        ? 'group bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow'
        : 'group bg-white rounded shadow overflow-hidden hover:shadow-md transition-shadow';
    $imageClass = $featured ? 'h-56 lg:h-64' : 'h-40';
@endphp

<article class="{{ $cardClass }}">
    <a href="{{ route('news.show', $article->slug) }}" class="block">
        @if($article->featured_image)
            <div class="relative overflow-hidden {{ $imageClass }}">
                <img src="{{ Storage::url($article->featured_image) }}"
                     alt="{{ $article->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @if($article->is_featured)
                    <span class="absolute top-2 left-2 bg-[#ff6600] text-white text-xs px-2 py-1 rounded">
                        {{ __('article.frontend.featured_news') }}
                    </span>
                @endif
            </div>
        @else
            <div class="relative {{ $imageClass }} bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
                @if($article->is_featured)
                    <span class="absolute top-2 left-2 bg-[#ff6600] text-white text-xs px-2 py-1 rounded">
                        {{ __('article.frontend.featured_news') }}
                    </span>
                @endif
            </div>
        @endif
    </a>

    <div class="p-4">
        {{-- Category Badge --}}
        @if($article->category)
            <a href="{{ route('news.category', $article->category->slug) }}"
               class="inline-block text-xs text-[#ff6600] hover:text-[#ff7700] font-medium mb-2">
                {{ $article->category->name }}
            </a>
        @endif

        {{-- Title --}}
        <h3 class="{{ $featured ? 'text-lg' : 'text-base' }} font-semibold mb-2 line-clamp-2 group-hover:text-[#ff6600] transition-colors">
            <a href="{{ route('news.show', $article->slug) }}">
                {{ $article->title }}
            </a>
        </h3>

        {{-- Excerpt --}}
        @if($article->excerpt && $featured)
            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                {{ $article->excerpt }}
            </p>
        @endif

        {{-- Meta Info --}}
        <div class="flex items-center text-xs text-gray-500 gap-3">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $article->published_at->format('d/m/Y') }}
            </span>

            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                {{ number_format($article->view_count) }}
            </span>

            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ $article->reading_time }} {{ __('article.frontend.min_read') }}
            </span>
        </div>
    </div>
</article>
