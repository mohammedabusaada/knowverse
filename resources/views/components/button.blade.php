@props([
    'href' => null,
    'primary' => false,
    'secondary' => false,
    'danger' => false,
    'size' => 'md', // sm, md, lg
])

@php
    // Monochrome Clean base
    $base = "inline-flex items-center justify-center font-medium rounded-lg
             transition-colors duration-150
             focus:outline-none focus:ring-2 focus:ring-blue-500/40
             focus:ring-offset-2 focus:ring-offset-white
             disabled:opacity-50 disabled:cursor-not-allowed
             dark:focus:ring-offset-gray-950";

    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-sm',   // رسمي/أكاديمي: text-sm أفضل من text-base
        'lg' => 'px-5 py-2.5 text-base',
    ];

    // ✅ الأزرق Accent واحد (Primary/Links focus)
    // ✅ باقي الأزرار Monochrome (رماديّات)
    // ✅ danger أحمر (حالة حذف/تحذير)
    $color = match (true) {
        $primary => "bg-blue-600 text-white hover:bg-blue-700
                     dark:bg-blue-500 dark:hover:bg-blue-600
                     border border-blue-600 dark:border-blue-500",

        $secondary => "bg-gray-900 text-white hover:bg-black
                       dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100
                       border border-gray-900 dark:border-white",

        $danger => "bg-red-600 text-white hover:bg-red-700
                    dark:bg-red-500 dark:hover:bg-red-600
                    border border-red-600 dark:border-red-500
                    focus:ring-red-500/40",

        default => "bg-white text-gray-900 hover:bg-gray-50
                    dark:bg-gray-900 dark:text-gray-100 dark:hover:bg-gray-800
                    border border-gray-200 hover:border-gray-300
                    dark:border-gray-800 dark:hover:border-gray-700"
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
