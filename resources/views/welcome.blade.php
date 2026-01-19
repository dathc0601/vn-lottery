@extends('layouts.base')

@section('title', 'Welcome - XSKT.VN')

@section('content')
<div class="bg-gray-50 py-8">
    <div class="container mx-auto px-4" style="max-width: 1400px;">
        <div class="max-w-4xl mx-auto">
            <!-- Laravel Welcome Content -->
            <div class="bg-white border border-gray-300 rounded-lg p-8 shadow-sm">
                <div class="text-center mb-8">
                    <h1 class="text-4xl font-bold text-gray-800 mb-2">Chào mừng đến với XSKT.VN</h1>
                    <p class="text-lg text-gray-600">Xổ số kiến thiết 3 miền - Kết quả nhanh nhất và chính xác nhất</p>
                </div>

                <!-- Quick Links -->
                <div class="grid md:grid-cols-3 gap-4 mb-8">
                    <a href="{{ route('xsmb') }}" class="block p-6 bg-gradient-to-br from-[#ff6600] to-[#ff8833] text-white rounded-lg hover:shadow-lg transition-shadow">
                        <h3 class="text-xl font-bold mb-2">XSMB</h3>
                        <p class="text-sm">Xổ số miền Bắc</p>
                    </a>
                    <a href="{{ route('xsmt') }}" class="block p-6 bg-gradient-to-br from-[#ff6600] to-[#ff8833] text-white rounded-lg hover:shadow-lg transition-shadow">
                        <h3 class="text-xl font-bold mb-2">XSMT</h3>
                        <p class="text-sm">Xổ số miền Trung</p>
                    </a>
                    <a href="{{ route('xsmn') }}" class="block p-6 bg-gradient-to-br from-[#ff6600] to-[#ff8833] text-white rounded-lg hover:shadow-lg transition-shadow">
                        <h3 class="text-xl font-bold mb-2">XSMN</h3>
                        <p class="text-sm">Xổ số miền Nam</p>
                    </a>
                </div>

                <!-- Laravel Info Section -->
                <div class="border-t border-gray-200 pt-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Laravel Application</h2>
                    <p class="text-gray-600 mb-4">This application is built with Laravel - The PHP Framework for Web Artisans.</p>

                    <div class="space-y-3">
                        <div class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-[#ff6600] rounded-full mt-2 mr-3"></span>
                            <div>
                                <p class="text-gray-700">
                                    Read the
                                    <a href="https://laravel.com/docs" target="_blank" class="text-[#ff6600] hover:underline font-medium">Documentation</a>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <span class="inline-block w-2 h-2 bg-[#ff6600] rounded-full mt-2 mr-3"></span>
                            <div>
                                <p class="text-gray-700">
                                    Watch video tutorials at
                                    <a href="https://laracasts.com" target="_blank" class="text-[#ff6600] hover:underline font-medium">Laracasts</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if (Route::has('login'))
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex gap-3">
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="inline-block px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#ff7700] transition-colors">
                                        Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-block px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                        Log in
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-block px-6 py-2 bg-[#ff6600] text-white rounded hover:bg-[#ff7700] transition-colors">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
