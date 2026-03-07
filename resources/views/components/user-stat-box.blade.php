@props([
    'value' => 0,
    'label' => '',
])

<div class="p-5 bg-paper rounded-sm border border-rule text-center transition-colors hover:border-ink">
    <div class="text-3xl font-heading font-black text-ink mb-1">
        {{ is_numeric($value) ? number_format($value) : $value }}
    </div>
    <div class="text-muted text-[10px] uppercase font-mono font-bold tracking-widest">
        {{ $label }}
    </div>
</div>