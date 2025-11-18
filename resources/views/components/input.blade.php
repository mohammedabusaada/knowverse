@props([
    'label' => null,
    'type' => 'text',
])

<div class="mb-4">
    @if($label)
        <label class="block mb-1 text-gray-700 dark:text-gray-300 font-semibold">
            {{ $label }}
        </label>
    @endif

    <input type="{{ $type }}"
           {{ $attributes->merge([
               'class' => 'w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white'
           ]) }}>
</div>
