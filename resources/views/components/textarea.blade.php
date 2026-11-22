@props([
    'label' => null,
    'rows' => 5,
])

<div class="mb-5 space-y-1.5">
    @if($label)
        <label for="{{ $attributes->get('id', $attributes->get('name')) }}" class="block text-sm font-serif font-bold text-ink">
            {{ $label }}
        </label>
    @endif

    <textarea
        rows="{{ $rows }}"
        {{ $attributes->merge([
            'id' => $attributes->get('id', $attributes->get('name')),
            'class' => '
                w-full rounded-md bg-transparent
                border border-rule text-ink font-serif text-sm px-4 py-3
                placeholder:text-muted
                focus:outline-none focus:ring-0 focus:border-ink
                transition-colors resize-y
            '
        ]) }}
    >{{ $slot }}</textarea>
</div>
