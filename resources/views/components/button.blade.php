@props([
    'href' => null,
    'primary' => false,
    'secondary' => false,
    'danger' => false,
    'size' => 'md',
])

@php
    $base = "inline-flex items-center justify-center font-serif font-bold transition-all focus:outline-none rounded-sm border border-transparent";

    $sizes = [
        'sm' => 'px-4 py-1.5 text-xs',
        'md' => 'px-6 py-2 text-sm tracking-wide',
        'lg' => 'px-8 py-2.5 text-base tracking-wide',
    ];

    $color = match (true) {
        $primary => "bg-ink text-paper border-ink hover:bg-transparent hover:text-ink",
        $secondary => "bg-transparent text-ink border-rule hover:border-ink hover:bg-aged/50",
        $danger => "bg-transparent text-[#a65a38] border-[#a65a38] hover:bg-[#a65a38] hover:text-paper",
        default => "bg-transparent text-ink hover:bg-aged/50"
    };

    $classes = trim("$base $color {$sizes[$size]}");
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif