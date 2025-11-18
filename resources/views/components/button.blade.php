@props([
    'href' => null,
    'primary' => false,
    'secondary' => false,
    'danger' => false,
    'size' => 'md', // sm, md, lg
])

@php
    $base = "inline-flex items-center justify-center font-medium rounded-lg transition
             focus:outline-none focus:ring-2 focus:ring-offset-2
             dark:focus:ring-offset-gray-900";

    $sizes = [
        'sm' => 'px-3 py-1 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];

    $color = match (true) {
        $primary => "bg-blue-600 text-white hover:bg-blue-700
                     dark:bg-blue-500 dark:hover:bg-blue-600
                     focus:ring-blue-500",

        $secondary => "bg-gray-200 text-gray-900 hover:bg-gray-300
                       dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600
                       focus:ring-gray-500",

        $danger => "bg-red-600 text-white hover:bg-red-700
                    dark:bg-red-500 dark:hover:bg-red-600
                    focus:ring-red-500",

        default => "bg-gray-100 text-gray-900 hover:bg-gray-200
                    dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700
                    focus:ring-gray-500"
    };

    $classes = "$base $color {$sizes[$size]}";
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
