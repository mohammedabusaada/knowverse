@props([
    'src' => null,
    'size' => 'md',  // sm, md, lg, xl
    'class' => ''
])

@php
    $sizes = [
        'sm' => 'w-6 h-6',
        'md' => 'w-8 h-8',
        'lg' => 'w-12 h-12',
        'xl' => 'w-32 h-32',
    ];

    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $fallback = asset('images/default-avatar.png');
@endphp

<img
    src="{{ $src ?: $fallback }}"
    alt="User Avatar"
    class="{{ $sizeClass }} rounded-full object-cover border border-gray-300 dark:border-gray-700 shadow {{ $class }}"
>
