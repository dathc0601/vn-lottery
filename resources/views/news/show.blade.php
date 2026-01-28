@extends('layouts.app')

@section('title', $article->meta_title ?: $article->title)

@section('meta_description', $article->meta_description ?: $article->excerpt ?: Str::limit(strip_tags($article->content), 160))

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('news.index') }}" class="text-[#0066cc] hover:underline">{{ __('article.frontend.news') }}</a>
    @if($article->category)
        <span class="mx-1">/</span>
        <a href="{{ route('news.category', $article->category->slug) }}" class="text-[#0066cc] hover:underline">{{ $article->category->name }}</a>
    @endif
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium truncate max-w-[200px] inline-block align-bottom">{{ $article->title }}</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <article class="bg-white rounded shadow overflow-hidden mb-4">
                {{-- Featured Image --}}
                @if($article->featured_image)
                    <div class="relative w-full h-64 md:h-80 lg:h-96">
                        <img src="{{ Storage::url($article->featured_image) }}"
                             alt="{{ $article->title }}"
                             class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-4 md:p-6">
                    {{-- Category Badge --}}
                    @if($article->category)
                        <a href="{{ route('news.category', $article->category->slug) }}"
                           class="inline-block text-sm text-white bg-[#ff6600] hover:bg-[#ff7700] px-3 py-1 rounded mb-3">
                            {{ $article->category->name }}
                        </a>
                    @endif

                    {{-- Title --}}
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                        {{ $article->title }}
                    </h1>

                    {{-- Meta Info --}}
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6 pb-4 border-b border-gray-200">
                        @if($article->author)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('article.frontend.by_author') }} <strong>{{ $article->author->name }}</strong>
                            </span>
                        @endif

                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $article->published_at->format('d/m/Y H:i') }}
                        </span>

                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ number_format($article->view_count) }} {{ __('article.frontend.views') }}
                        </span>

                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $article->reading_time }} {{ __('article.frontend.min_read') }}
                        </span>
                    </div>

                    {{-- Excerpt --}}
                    @if($article->excerpt)
                        <div class="text-lg text-gray-700 mb-6 font-medium italic border-l-4 border-[#ff6600] pl-4">
                            {{ $article->excerpt }}
                        </div>
                    @endif

                    {{-- Content --}}
                    <div class="prose prose-lg max-w-none
                                prose-headings:text-gray-900 prose-headings:font-semibold
                                prose-h2:text-xl prose-h2:mt-8 prose-h2:mb-4
                                prose-h3:text-lg prose-h3:mt-6 prose-h3:mb-3
                                prose-p:text-gray-700 prose-p:leading-relaxed prose-p:mb-4
                                prose-a:text-[#0066cc] prose-a:hover:text-[#ff6600]
                                prose-strong:text-gray-900
                                prose-ul:my-4 prose-ul:pl-6
                                prose-ol:my-4 prose-ol:pl-6
                                prose-li:text-gray-700 prose-li:mb-1
                                prose-blockquote:border-l-4 prose-blockquote:border-[#ff6600] prose-blockquote:pl-4 prose-blockquote:italic
                                prose-img:rounded prose-img:shadow">
                        {!! $article->content !!}
                    </div>

                    {{-- Share Section --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-medium text-gray-700">{{ __('article.frontend.share') }}:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-8 h-8 bg-blue-600 text-white rounded flex items-center justify-center hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.77 7.46H14.5v-1.9c0-.9.6-1.1 1-1.1h3V.5h-4.33C10.24.5 9.5 3.44 9.5 5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4z"/>
                                </svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-8 h-8 bg-black text-white rounded flex items-center justify-center hover:bg-gray-800 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                </svg>
                            </a>
                            <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="w-8 h-8 bg-sky-500 text-white rounded flex items-center justify-center hover:bg-sky-600 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </article>

            {{-- Related Articles --}}
            @if($relatedArticles->count() > 0)
                <div class="mb-4">
                    <div class="bg-white rounded shadow overflow-hidden">
                        <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                            {{ __('article.frontend.related_news') }}
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($relatedArticles as $related)
                                    <x-news.article-card :article="$related" />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Back to List --}}
            <div class="text-center">
                <a href="{{ route('news.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('article.frontend.back_to_list') }}
                </a>
            </div>

        </div>

        <!-- Right Sidebar -->
        <x-news.sidebar
            :categories="$categories"
            :popularArticles="$popularArticles"
        />
    </div>
</div>
@endsection
