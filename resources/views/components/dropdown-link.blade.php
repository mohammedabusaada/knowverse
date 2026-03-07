@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'block w-full px-4 py-1.5 text-sm font-serif text-accent font-bold bg-aged/50 transition-colors'
            : 'block w-full px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged hover:text-accent transition-colors';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>