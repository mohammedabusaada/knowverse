@props(['active' => false, 'icon' => null])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 w-full px-4 py-2 text-sm leading-5 text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 focus:outline-none transition duration-150 ease-in-out'
            : 'flex items-center gap-3 w-full px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes . ' group']) }}>
    @if($icon)
        <x-dynamic-component :component="'icons.' . $icon" 
            class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors" 
        />
    @endif
    
    <span>{{ $slot }}</span>
</a>