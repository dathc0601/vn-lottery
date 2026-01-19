<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kết Quả Xổ Số - XSKT.VN')</title>

    <!-- Google Fonts - Roboto + Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-gray-800 antialiased">
    <!-- Dark Header - xskt.net style -->
    <header class="bg-[#2d2d2d] text-white py-3">
        <div class="container mx-auto px-4" style="max-width: 1400px;">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('home') }}" class="block">
                        <h1 class="text-xl md:text-2xl font-bold text-[#ff6600]">XSKT.VN</h1>
                        <p class="text-xs text-gray-300 mt-0.5">Số chuẩn xác - May mắn phát</p>
                    </a>
                </div>
                <div class="text-right">
                    <p class="text-sm">Hôm nay: {{ now()->locale('vi')->isoFormat('dddd [ngày] DD/MM/YYYY') }}</p>
                </div>
            </div>
        </div>
    </header>

    <!-- Orange Navigation Bar - xskt.net style -->
    <nav class="bg-[#ff6600]">
        <div class="container mx-auto px-4" style="max-width: 1400px;">
            <ul class="flex items-center h-10 flex-wrap">
                <li>
                    <a href="{{ route('home') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('home') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('home') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Trang chủ
                    </a>
                </li>
                <li>
                    <a href="{{ route('xsmb') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('xsmb') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('xsmb') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        XSMB
                    </a>
                </li>
                <li>
                    <a href="{{ route('xsmt') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('xsmt') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('xsmt') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        XSMT
                    </a>
                </li>
                <li>
                    <a href="{{ route('xsmn') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('xsmn') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('xsmn') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        XSMN
                    </a>
                </li>
                <li>
                    <a href="{{ route('results.book') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('results.book') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('results.book') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Sổ kết quả
                    </a>
                </li>
                <li>
                    <a href="{{ route('statistics') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('statistics') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('statistics') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Thống kê
                    </a>
                </li>
                <li>
                    <a href="{{ route('ticket.verify') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('ticket.verify') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('ticket.verify') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Dò vé số
                    </a>
                </li>
                <li>
                    <a href="{{ route('schedule') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('schedule') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('schedule') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Lịch mở thưởng
                    </a>
                </li>
                <li>
                    <a href="{{ route('trial.draw') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('trial.draw') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('trial.draw') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Quay thử
                    </a>
                </li>
                <li>
                    <a href="{{ route('vietlott') }}"
                       class="block px-4 text-white font-medium transition-colors duration-200 text-sm
                              {{ request()->routeIs('vietlott') ? 'bg-black bg-opacity-15' : '' }}"
                       style="line-height: 40px;"
                       onmouseover="this.style.background='rgba(0,0,0,0.15)'"
                       onmouseout="this.style.background='{{ request()->routeIs('vietlott') ? 'rgba(0,0,0,0.15)' : 'transparent' }}'">
                        Vietlott
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Breadcrumb (optional) -->
    @hasSection('breadcrumb')
    <div class="bg-gray-50 border-b border-gray-200">
        <div class="container mx-auto px-4 py-2 text-sm" style="max-width: 1400px;">
            @yield('breadcrumb')
        </div>
    </div>
    @endif

    <!-- Main Content (flexible) -->
    @yield('content')

    <!-- Footer - Dark Theme -->
    <footer class="bg-[#333333] text-white mt-12">
        <div class="container mx-auto px-4 py-8" style="max-width: 1400px;">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-3">XSKT.VN</h3>
                    <p class="text-sm text-gray-300">Trang web cung cấp kết quả xổ số 3 miền nhanh nhất và chính xác nhất</p>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-3">Liên kết nhanh</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('xsmb') }}" class="text-gray-300 hover:text-[#ff6600] hover:underline transition-colors">XSMB</a></li>
                        <li><a href="{{ route('xsmt') }}" class="text-gray-300 hover:text-[#ff6600] hover:underline transition-colors">XSMT</a></li>
                        <li><a href="{{ route('xsmn') }}" class="text-gray-300 hover:text-[#ff6600] hover:underline transition-colors">XSMN</a></li>
                        <li><a href="{{ route('results.book') }}" class="text-gray-300 hover:text-[#ff6600] hover:underline transition-colors">Sổ kết quả</a></li>
                        <li><a href="{{ route('statistics') }}" class="text-gray-300 hover:text-[#ff6600] hover:underline transition-colors">Thống kê</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-3">Thông tin</h3>
                    <p class="text-sm text-gray-300">&copy; {{ date('Y') }} XSKT.VN</p>
                    <p class="text-xs text-gray-400 mt-2">Kết quả chỉ mang tính chất tham khảo</p>
                    <p class="text-xs text-gray-400 mt-1">Số chuẩn xác - May mắn phát</p>
                </div>
            </div>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
