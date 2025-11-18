@props([
    'count' => 0,
    'label' => '',
])

<div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg shadow text-center">
    <div class="text-2xl font-bold text-gray-900 dark:text-white">
        {{ $count }}
    </div>
    <div class="text-gray-600 dark:text-gray-400 text-sm">
        {{ $label }}
    </div>
</div>
