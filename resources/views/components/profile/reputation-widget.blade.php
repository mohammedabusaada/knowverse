@props(['user'])

<div class="flex items-center gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60
            border border-gray-300 dark:border-gray-700 shadow-sm">

    <div>
        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 leading-none">
            {{ $user->reputation_points }}
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400">
            Reputation Points
        </div>
    </div>
</div>
