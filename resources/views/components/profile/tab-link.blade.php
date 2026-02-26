@props(['href', 'active' => false])

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'pb-4 px-2 font-bold text-base whitespace-nowrap border-b-4 transition-all duration-200 ' . 
   ($active 
       ? 'border-black text-black dark:text-white dark:border-white' 
       : 'border-transparent text-gray-500 hover:text-black dark:text-gray-400 dark:hover:text-white')]) }}>
    {{ $slot }}
</a>