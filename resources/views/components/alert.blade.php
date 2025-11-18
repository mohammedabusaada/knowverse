@props([
    'type' => 'success'
])

@php
$colors = [
    'success' => 'bg-green-100 text-green-800 border-green-300',
    'error'   => 'bg-red-100 text-red-800 border-red-300',
];
@endphp

<div {{ $attributes->merge([
        'class' => "border px-4 py-3 rounded-lg mb-4 {$colors[$type]}"
    ]) }}>
    {{ $slot }}
</div>
