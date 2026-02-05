@props([
    'label' => null,
])

<div class="mb-4">
    @if($label)
        <label class="block mb-1 text-sm font-medium
                       text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <select
        {{ $attributes->merge([
            'class' => '
                w-full
                rounded-lg
                bg-white dark:bg-gray-900
                border border-gray-300 dark:border-gray-700
                text-gray-900 dark:text-gray-100
                text-sm
                focus:outline-none
                focus:border-blue-600
                focus:ring-2 focus:ring-blue-500/30
                transition
            '
        ]) }}
    >
        {{ $slot }}
    </select>
</div>
