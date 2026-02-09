@props(['href', 'active' => false])

<a href="{{ $href }}" 
   {{ $attributes->merge(['class' => 'pb-4 px-2 font-semibold text-base whitespace-nowrap border-b-2 transition-all duration-300 ' . 
   ($active 
       ? 'border-blue-600 text-blue-700 dark:text-blue-400 dark:border-blue-500' 
       : 'border-transparent text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200')]) }}>
    {{ $slot }}
</a>