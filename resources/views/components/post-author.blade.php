@props(['user', 'date'])

<div class="flex items-center mb-6">
    <x-user-avatar :src="$user->profile_picture_url" size="48" />

    <div class="ml-4">
        <p class="font-semibold text-gray-900 dark:text-gray-100">
            {{ $user->display_name }}
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Posted {{ $date }}
        </p>
    </div>
</div>
