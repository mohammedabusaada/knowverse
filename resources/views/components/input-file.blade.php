@props([
    'label' => null,
    'name' => null,
])

<div class="mb-5">
    @if ($label)
        <label class="block text-gray-700 dark:text-gray-300 font-semibold mb-2">
            {{ $label }}
        </label>
    @endif

    <input
        type="file"
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => '
                block w-full text-gray-700 dark:text-gray-300
                file:mr-4 file:py-2 file:px-4
                file:rounded-lg file:border-0
                file:bg-blue-600 file:text-white
                hover:file:bg-blue-700
                dark:file:bg-blue-500 dark:hover:file:bg-blue-600
            '
        ]) }}
    >

    @error($name)
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
