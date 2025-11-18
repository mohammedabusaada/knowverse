@props([
    'placeholder' => 'Search...',
    'action' => route('search') // optional; can override if needed
])

<form action="{{ route('search') }}" method="GET" class="relative w-full">

    <input
        type="text"
        name="q"
        placeholder="{{ $placeholder }}"
        class="w-full pl-11 pr-4 py-2.5 rounded-full text-sm
               border border-gray-300 dark:border-gray-700
               bg-gray-50 dark:bg-gray-800
               text-gray-800 dark:text-gray-200
               placeholder-gray-500 dark:placeholder-gray-400
               focus:ring-2 focus:ring-blue-500 focus:border-blue-500
               shadow-sm transition"
    >

    <!-- Search Icon -->
    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500 dark:text-gray-400"
         fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="7" stroke-width="2" />
        <path stroke-width="2" d="M20 20l-3.5-3.5" />
    </svg>

</form>
