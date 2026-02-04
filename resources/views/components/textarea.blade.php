@props([
    'label' => null,
    'rows' => 5,
])

<div class="mb-5">
    @if($label)
        <label class="block mb-1 text-sm font-semibold text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    <textarea
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'class' => '
                w-full rounded-lg
                bg-white dark:bg-gray-900
                border border-gray-300 dark:border-gray-700
                text-gray-900 dark:text-gray-100
                placeholder-gray-400 dark:placeholder-gray-500
                focus:outline-none
                focus:ring-2 focus:ring-blue-500/40
                focus:border-blue-500
                transition
            '
        ]) }}
    >{{ $slot }}</textarea>
</div>
