@props([
    'label' => null,
    'type' => 'text',
])

<div class="mb-4">
    @if($label)
        <label class="block mb-1 text-sm font-medium
                       text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        {{ $attributes->merge([
            'class' => '
                w-full
                rounded-lg
                bg-white dark:bg-gray-900
                border border-gray-300 dark:border-gray-700
                text-gray-900 dark:text-gray-100
                placeholder-gray-400 dark:placeholder-gray-500
                text-sm
                focus:outline-none
                focus:border-blue-600
                focus:ring-2 focus:ring-blue-500/30
                transition
            '
        ]) }}
    >
</div>
