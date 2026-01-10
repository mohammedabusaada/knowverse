@props([
    'message' => 'No results found.',
    'icon' => 'search',
])

<div class="p-12 text-center
            bg-white/80 dark:bg-gray-800/80
            backdrop-blur
            rounded-2xl
            border border-dashed
            border-gray-300 dark:border-gray-700
            shadow-sm">

    <!-- Icon -->
    <div class="flex justify-center mb-5">
        @if ($icon === 'search')
            <svg class="w-11 h-11 text-gray-400 dark:text-gray-500"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" stroke-width="2" />
                <path stroke-width="2" d="M20 20l-3.5-3.5" />
            </svg>
        @elseif ($icon === 'users')
            <svg class="w-11 h-11 text-gray-400 dark:text-gray-500"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M17 20h5v-2a4 4 0 00-4-4h-1" />
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M9 20H4v-2a4 4 0 014-4h1" />
                <circle cx="9" cy="7" r="4" stroke-width="2" />
                <circle cx="17" cy="7" r="4" stroke-width="2" />
            </svg>
        @elseif ($icon === 'tag')
            <svg class="w-11 h-11 text-gray-400 dark:text-gray-500"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                      d="M7 7h.01M3 11l8.586 8.586a2 2 0 002.828 0l6.586-6.586a2 2 0 000-2.828L13 3H3v8z" />
            </svg>
        @endif
    </div>

    <!-- Message -->
    <p class="text-sm font-medium
              text-gray-600 dark:text-gray-300">
        {{ $message }}
    </p>
</div>
