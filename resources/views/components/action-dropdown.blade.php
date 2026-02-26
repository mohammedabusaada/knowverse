<div x-data="{ open: false }" class="relative inline-block text-left">
    <button
        @click="open = !open"
        class="px-2 py-1 text-muted hover:text-ink transition-colors focus:outline-none">
        <span class="font-serif text-lg leading-none tracking-widest">...</span>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 translate-y-1"
        x-transition:enter-end="transform opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 translate-y-0"
        x-transition:leave-end="transform opacity-0 translate-y-1"
        class="absolute right-0 mt-2 w-48 bg-paper border border-rule shadow-xl z-50 rounded-sm"
        x-cloak>
        <div class="py-1 text-sm font-serif">
            {{ $slot }}
        </div>
    </div>
</div>