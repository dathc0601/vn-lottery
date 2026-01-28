@extends('layouts.app')

@section('title', __('article.frontend.news') . ' - Tin tức xổ số')

@section('meta_description', 'Cập nhật tin tức xổ số mới nhất, thông tin về các giải thưởng, lịch quay số và nhiều thông tin hữu ích khác.')

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">{{ __('article.frontend.news') }}</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <!-- Page Header (Orange bar) -->
            <div class="bg-white rounded shadow overflow-hidden mb-4">
                <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                    {{ __('article.frontend.news') }} - Cập nhật thông tin xổ số
                </div>
            </div>

            {{-- Featured Articles Section --}}
            @if($featuredArticles->count() > 0)
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#ff6600]" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        {{ __('article.frontend.featured_news') }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($featuredArticles as $article)
                            <x-news.article-card :article="$article" :featured="true" />
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- All Articles Section --}}
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-[#ff6600]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    {{ __('article.frontend.all_news') }}
                </h2>

                @if($articles->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($articles as $article)
                            <x-news.article-card :article="$article" />
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($articles->hasPages())
                        <div class="mt-6">
                            {{ $articles->links() }}
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded p-6 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                        <p class="text-gray-600">{{ __('article.frontend.no_articles') }}</p>
                    </div>
                @endif
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
