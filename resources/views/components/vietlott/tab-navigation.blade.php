@props(['activeGame' => 'mega645'])

<div class="vietlott-tabs">
    <a href="{{ route('vietlott.mega645') }}"
       class="vietlott-tab {{ $activeGame === 'mega645' ? 'active' : '' }}">
        Mega 6/45
    </a>
    <a href="{{ route('vietlott.power655') }}"
       class="vietlott-tab {{ $activeGame === 'power655' ? 'active' : '' }}">
        Power 6/55
    </a>
    <a href="{{ route('vietlott.max3d') }}"
       class="vietlott-tab {{ $activeGame === 'max3d' ? 'active' : '' }}">
        Max 3D
    </a>
    <a href="{{ route('vietlott.max3dpro') }}"
       class="vietlott-tab {{ $activeGame === 'max3dpro' ? 'active' : '' }}">
        Max 3D Pro
    </a>
</div>
