<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 2500)"
    x-show="show"
    x-transition.opacity.duration.300ms
    class="fixed bottom-6 right-6 z-50"
>
    <div class="flex items-center gap-2 px-4 py-2 rounded-lg
                bg-gray-900 text-white text-sm shadow-lg">

        <span class="text-green-400 font-semibold">+</span>

        <span class="font-medium text-green-400">
            {{ $slot }}
        </span>
    </div>
</div>
