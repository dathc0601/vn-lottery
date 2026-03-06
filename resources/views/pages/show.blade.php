@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)

@section('meta_description', $page->meta_description ?: Str::limit(strip_tags($page->content), 160))

@section('breadcrumb')
    <a href="{{ route('home') }}" class="text-[#0066cc] hover:underline">Trang chủ</a>
    <span class="mx-1">/</span>
    <span class="text-gray-800 font-medium truncate max-w-[200px] inline-block align-bottom">{{ $page->title }}</span>
@endsection

@section('page-content')
<div>
    <!-- Two-Column Layout -->
    <div class="flex flex-col lg:flex-row gap-4">

        <!-- Main Content Column -->
        <div class="flex-1 min-w-0">

            <article class="bg-white rounded shadow overflow-hidden mb-4">
                {{-- Featured Image --}}
                @if($page->featured_image)
                    <div class="relative w-full h-64 md:h-80 lg:h-96">
                        <img src="{{ Storage::url($page->featured_image) }}"
                             alt="{{ $page->title }}"
                             class="w-full h-full object-cover">
                    </div>
                @endif

                <div class="p-4 md:p-6">
                    {{-- Title --}}
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">
                        {{ $page->title }}
                    </h1>

                    {{-- Meta Info --}}
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-6 pb-4 border-b border-gray-200">
                        @if($page->published_at)
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ $page->published_at->format('d/m/Y') }}
                        </span>
                        @endif

                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            {{ number_format($page->view_count) }} lượt xem
                        </span>
                    </div>

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
                        {!! $page->rendered_content !!}
                    </div>
                </div>
            </article>

            {{-- Related Pages --}}
            @if($relatedPages->count() > 0)
                <div class="mb-4">
                    <div class="bg-white rounded shadow overflow-hidden">
                        <div class="bg-[#ff6600] text-white px-4 py-2 font-medium">
                            Trang liên quan
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                @foreach($relatedPages as $related)
                                    <li>
                                        <a href="{{ route('page.show', $related->slug) }}"
                                           class="text-[#0066cc] hover:underline">
                                            {{ $related->title }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Sidebar -->
        <x-lottery-sidebar
            :northProvinces="$northProvinces"
            :centralProvinces="$centralProvinces"
            :southProvinces="$southProvinces"
        />
    </div>
</div>
@endsection
