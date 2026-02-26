@props(['type' => 'success'])

@php
$colors = [
    'success' => 'border-ink text-ink bg-aged/50',
    'error'   => 'border-[#a65a38] text-[#a65a38] bg-[#a65a38]/5',
];
@endphp

<div {{ $attributes->merge(['class' => "border-l-4 p-4 mb-6 font-serif text-[15px] italic leading-relaxed {$colors[$type]}"]) }}>
    {{ $slot }}
</div>