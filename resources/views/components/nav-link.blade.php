@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block px-3 py-1.5 text-[17px] font-serif italic text-accent border-l-2 border-accent bg-aged/30 transition-all duration-200'
            : 'block px-3 py-1.5 text-[17px] font-serif text-muted hover:text-ink hover:bg-aged/20 border-l-2 border-transparent hover:border-rule transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>