@props(['href', 'active' => false])

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'pb-4 px-2 font-bold text-base whitespace-nowrap border-b-4 transition-all duration-200 ' . 
   ($active 
       ? 'border-ink text-ink' 
       : 'border-transparent text-muted hover:text-ink')]) }}>
    {{ $slot }}
</a>