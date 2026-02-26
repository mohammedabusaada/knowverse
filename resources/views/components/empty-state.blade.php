@props([
    'title' => 'Nothing here',
    'description' => null,
])

<div class="text-center py-20 border border-dashed border-rule rounded-sm bg-aged/10">
    <h3 class="font-heading text-xl font-bold text-ink mb-3">
        ✦ &nbsp; {{ $title }} &nbsp; ✦
    </h3>

    @if ($description)
        <p class="font-serif text-[15px] text-muted italic">
            {{ $description }}
        </p>
    @endif
</div>