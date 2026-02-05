<div {{ $attributes->merge([
    'class' => '
        bg-white dark:bg-gray-900
        border border-gray-200 dark:border-gray-800
        rounded-xl
        shadow-sm
        p-4 sm:p-6
    '
]) }}>
    {{ $slot }}
</div>
