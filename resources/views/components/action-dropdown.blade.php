<div x-data="{ open: false }" class="relative">

    <button
        @click="open = !open"
        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
        ⋯
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-800
               border border-gray-200 dark:border-gray-700
               rounded-xl shadow-lg z-50">

        <div class="py-1 text-sm">

            {{ $slot }}

        </div>
    </div>
</div>
