@props(['active'])

@php
$classes = ($active ?? false)
            ? 'group flex items-center gap-3 px-3 py-1.5 text-[17px] font-serif italic text-ink border-l-2 border-ink bg-aged/30 transition-all duration-200'
            : 'group flex items-center gap-3 px-3 py-1.5 text-[17px] font-serif text-muted hover:text-ink hover:bg-aged/20 border-l-2 border-transparent hover:border-rule transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if (isset($icon))
        <span class="shrink-0 {{ ($active ?? false) ? 'text-ink' : 'text-muted opacity-60 group-hover:opacity-100 group-hover:text-ink' }} transition-colors">
            {{ $icon }}
        </span>
    @endif
    
    <span class="truncate">{{ $slot }}</span>
</a>