<div x-data="{ open: false }" class="relative inline-block text-left">
    <button
        @click="open = !open"
        class="p-2 text-muted hover:text-ink hover:bg-aged/50 rounded-sm transition-colors focus:outline-none flex items-center justify-center"
        title="More Actions"
    >
        {{-- Professional Vertical Dots Icon --}}
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
        </svg>
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
        x-cloak
    >
        <div class="py-1 text-sm font-serif">
            {{ $slot }}
        </div>
    </div>
</div>