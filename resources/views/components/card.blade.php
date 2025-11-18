<div {{ $attributes->merge([
        'class' => 'bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 p-6'
    ]) }}>
    {{ $slot }}
</div>
