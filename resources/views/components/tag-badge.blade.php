@props(['tag' => null, 'label' => null])

@php
    $displayText = strtolower($tag ? $tag->name : $label);
    // If it's a real tag object, link to its show page, otherwise fallback to #
    $url = $tag ? route('tags.show', $tag->slug) : '#';
@endphp

<a href="{{ $url }}" {{ $attributes->merge(['class' => 'font-mono text-[11px] tracking-wider text-ink bg-aged border border-rule rounded-sm px-3 py-1 hover:bg-ink hover:text-paper transition-colors inline-flex items-center decoration-none cursor-pointer']) }}>
    <span class="opacity-40 mr-1.5 font-serif text-sm leading-none">§</span> 
    {{ $displayText }}
</a>