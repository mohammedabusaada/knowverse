@props([
    'title' => 'Nothing here',
    'description' => null,
])

<div class="text-center py-16">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
        {{ $title }}
    </h3>

    @if ($description)
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            {{ $description }}
        </p>
    @endif
</div>
