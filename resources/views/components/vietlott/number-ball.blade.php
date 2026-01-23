@props([
    'number',
    'variant' => 'default', // default, special (for Power 6/55 7th number)
    'size' => 'normal' // normal, small
])

@php
    $sizeClasses = $size === 'small' ? 'w-8 h-8 text-sm' : 'w-11 h-11 text-base';
    $borderColor = $variant === 'special' ? 'border-red-500' : 'border-[#ff6600]';
    $textColor = $variant === 'special' ? 'text-red-600' : 'text-black';
@endphp

<span class="vietlott-ball {{ $sizeClasses }} {{ $borderColor }} {{ $textColor }}">
    {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
</span>
