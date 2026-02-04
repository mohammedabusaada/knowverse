@props([
    'name' => null,
    'show' => false,
    'maxWidth' => '2xl'
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{ show: false }"
    x-on:open-modal.window="if ($event.detail === '{{ $name }}') show = true"
    x-on:close-modal.window="if ($event.detail === '{{ $name }}') show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center px-4"
>

    {{-- Overlay --}}
    <div
        class="absolute inset-0 bg-gray-500/75"
        @click="show = false"
    ></div>

    {{-- Modal --}}
    <div
        @click.stop
        class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full {{ $maxWidth }} p-6 z-10"
        x-transition
    >
        {{ $slot }}
    </div>
</div>
