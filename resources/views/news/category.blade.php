@extends('layouts.app')

@section('title', $category->meta_title ?: $category->name . ' - ' . __('article.frontend.news'))

@section('meta_description', $category->meta_description ?: $category->description ?: 'Danh sách bài viết trong danh mục ' . $category->name)

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <a href="{{ route('news.index') }}" class="text-[#0066cc] hover:underline">{{ __('article.frontend.news') }}</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium">{{ $category->name }}</span>
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
                    {{ $category->name }}
                </div>
                @if($category->description)
                    <div class="px-4 py-3 text-sm text-gray-600 border-b">
                        {{ $category->description }}
                    </div>
                @endif
            </div>

            {{-- Articles Section --}}
            <div class="mb-6">
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

            {{-- Back to All News --}}
            <div class="text-center">
                <a href="{{ route('news.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('article.frontend.all_news') }}
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
