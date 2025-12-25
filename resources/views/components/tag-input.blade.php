@props([
    'label' => null,
    'options' => [],
    'selected' => [],
])

@php
    $isMultiple = $attributes->has('multiple');
    $selectedValues = collect($selected)->map(fn($v) => (string) $v)->toArray();
@endphp

<div class="mb-4">
    @if($label)
        <label class="block mb-1 text-gray-700 dark:text-gray-300 font-semibold">
            {{ $label }}
        </label>
    @endif

    <select
        {{ $attributes->merge([
            'name' => $isMultiple ? 'tag_ids[]' : 'tag_id',
            'class' =>
                'w-full rounded-lg border-gray-300 dark:border-gray-700
                 dark:bg-gray-900 dark:text-white'
        ]) }}
        @if($isMultiple) multiple @endif
    >

        @unless($isMultiple)
            <option value="">Select a tag</option>
        @endunless

        @foreach($options as $option)
            <option value="{{ $option->id }}"
                {{ in_array((string) $option->id, $selectedValues, true) ? 'selected' : '' }}>
                {{ $option->name }}
            </option>
        @endforeach

    </select>

    @if($isMultiple)
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            Hold Ctrl (or Cmd) to select multiple topics
        </p>
    @endif
</div>
