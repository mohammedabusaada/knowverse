@props([
    'label' => null,
    'options' => [],  // <-- هنا نرسل التاقات من الكنترولر
    'selected' => null,
])

<div class="mb-4">
    @if($label)
        <label class="block mb-1 text-gray-700 dark:text-gray-300 font-semibold">
            {{ $label }}
        </label>
    @endif

    <select
        {{ $attributes->merge([
            'class' =>
                'w-full rounded-lg border-gray-300 dark:border-gray-700
                 dark:bg-gray-900 dark:text-white'
        ]) }}>

        <option value=""></option>

        @foreach($options as $option)
            <option value="{{ $option->id }}"
                {{ (string)$selected === (string)$option->id ? 'selected' : '' }}>
                {{ $option->name }}
            </option>
        @endforeach

    </select>
</div>
