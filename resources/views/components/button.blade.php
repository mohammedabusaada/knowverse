@props([
    'href' => null,
    'primary' => false,
    'secondary' => false,
    'danger' => false,
    'size' => 'md',
])

@php
    $base = "inline-flex items-center justify-center font-serif font-semibold transition-colors focus:outline-none rounded-md border disabled:opacity-50 disabled:cursor-not-allowed";

    $sizes = [
        'sm' => 'min-h-11 px-4 py-2 text-sm',
        'md' => 'min-h-11 px-5 py-2.5 text-sm',
        'lg' => 'min-h-12 px-6 py-3 text-base',
    ];

    $color = match (true) {
        $primary => "bg-ink text-paper border-ink hover:bg-transparent hover:text-ink",
        $secondary => "bg-transparent text-ink border-rule hover:border-ink hover:bg-aged/50",
        $danger => "bg-transparent text-ink border-ink border-dashed hover:bg-ink hover:text-paper",
        default => "bg-transparent text-ink border-transparent hover:bg-aged/50"
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