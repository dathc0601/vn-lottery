@extends('layouts.base')

@section('content')
<!-- Three Column Layout -->
<div class="bg-gray-50 py-4">
    <div class="three-column-container" style="max-width: 1040px;">
        <!-- Main Content -->
        <main>
            @yield('page-content')
        </main>

        <!-- Left Sidebar (Quick Navigation) -->
        @hasSection('left-sidebar')
            <aside class="left-sidebar">
                @yield('left-sidebar')
            </aside>
        @endif

        <!-- Right Sidebar -->
        @hasSection('right-sidebar')
        <aside class="right-sidebar">
            @yield('right-sidebar')
        </aside>
        @endif
    </div>
</div>
@endsection
