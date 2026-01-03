<div class="text-center py-12">

    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
        {{ $title }}
    </h3>

    @if (!empty($description))
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ $description }}
        </p>
    @endif

    @isset($slot)
        <div class="mt-6">
            {{ $slot }}
        </div>
    @endisset

</div>
