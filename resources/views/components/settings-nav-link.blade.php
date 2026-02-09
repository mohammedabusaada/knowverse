@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2 text-sm font-semibold text-blue-600 bg-blue-50 dark:bg-blue-900/20 rounded-lg'
            : 'flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>