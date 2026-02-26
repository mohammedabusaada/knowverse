@props([
    'message' => 'No results found.',
    'icon' => 'search',
])

<div class="py-16 px-6 text-center border border-dashed border-rule bg-aged/10 rounded-sm">
    <p class="font-serif text-lg text-muted italic">
        "{{ $message }}"
    </p>
</div>