@props([
    'label' => null,
    'type' => 'text',
])

<div class="space-y-1.5">
    @if($label)
        <label for="{{ $attributes->get('id', $attributes->get('name')) }}" class="block text-sm font-serif font-bold text-ink">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        {{ $attributes->merge([
            'id' => $attributes->get('id', $attributes->get('name')),
            'class' => 'w-full rounded-md border border-rule bg-transparent px-4 py-2.5 text-sm text-ink font-serif
                        placeholder:text-muted
                        focus:border-ink focus:ring-0 focus:outline-none transition-colors'
        ]) }}
    >
</div>
