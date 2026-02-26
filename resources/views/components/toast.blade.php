<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-4"
    class="fixed bottom-8 right-8 z-50"
    x-cloak
>
    <div class="flex items-center gap-3 px-5 py-3 rounded-sm
                bg-ink text-paper border border-rule shadow-xl">
        <span class="font-mono text-accent-warm font-bold">✦</span>
        <span class="font-serif text-sm tracking-wide">
            {{ $slot }}
        </span>
    </div>
</div>