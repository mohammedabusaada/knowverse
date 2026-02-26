@props([
    'value' => 0,
    'label' => '',
])

<div class="p-5 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 text-center transition hover:border-black dark:hover:border-white">
    <div class="text-3xl font-black text-black dark:text-white mb-1">
        {{ $value }}
    </div>
    <div class="text-gray-500 dark:text-gray-400 text-[10px] uppercase font-bold tracking-widest">
        {{ $label }}
    </div>
</div>