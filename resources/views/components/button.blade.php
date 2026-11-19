{{-- variant: primary | secondary · icon: nama ikon, mis. arrow-up-right · href: render sebagai <a> --}}
@props([
    'variant' => 'primary',
    'href' => null,
    'icon' => null,
    'block' => false,
])

@php
    $classes = 'btn btn--' . $variant . ($block ? ' btn--block' : '');
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        <span>{{ $slot }}</span>
        @if ($icon) <x-icon :name="$icon" :size="16" /> @endif
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes, 'type' => 'button']) }}>
        <span>{{ $slot }}</span>
        @if ($icon) <x-icon :name="$icon" :size="16" /> @endif
    </button>
@endif
