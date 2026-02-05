@props([
    'label' => null,
    'type' => 'text',
])

<div class="space-y-2">
    @if($label)
        <label class="block text-sm font-semibold text-gray-800 dark:text-gray-200">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm
                        placeholder:text-gray-400
                        focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 focus:outline-none
                        dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500
                        dark:focus:border-blue-400 dark:focus:ring-blue-400/20'
        ]) }}
    >
</div>
