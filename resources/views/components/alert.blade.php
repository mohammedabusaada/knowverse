@props(['type' => 'info'])

@php
$styles = [
    'success' => 'border-accent text-accent bg-accent/5', // Blue for success
    'error'   => 'border-accent-warm text-accent-warm bg-accent-warm/5', // Brown for error
    'info'    => 'border-muted text-muted bg-aged/10',
];

$selectedStyle = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge(['class' => "border-l-2 p-4 mb-6 font-serif text-[15px] italic leading-relaxed shadow-sm {$selectedStyle}"]) }}>
    {{ $slot }}
</div>