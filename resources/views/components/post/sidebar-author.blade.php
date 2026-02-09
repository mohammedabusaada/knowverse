@props(['user'])

<div class="sticky top-24 space-y-6">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm">
        <h3 class="text-xs font-semibold uppercase tracking-widest text-gray-700 dark:text-gray-300 mb-4">About the Author</h3>
        <div class="flex items-center gap-3 mb-4">
            <x-user-avatar :src="$user->profile_picture_url" size="lg" />
            <div class="min-w-0">
                <p class="font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $user->display_name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ '@'.$user->username }}</p>
            </div>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-4">
            {{ $user->bio ?? 'This user hasn\'t shared a bio yet.' }}
        </p>
        <a href="{{ route('profile.show', $user->username) }}" class="block text-center py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-sm font-semibold text-gray-800 dark:text-gray-100 transition">
            View Profile
        </a>
    </div>

    <div class="p-4 text-[11px] text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800">
        <h4 class="font-semibold uppercase tracking-wider mb-2 text-gray-700 dark:text-gray-300">Rules</h4>
        <ul class="list-disc ml-4 space-y-1">
            <li>Be respectful.</li>
            <li>No spam.</li>
            <li>Cite sources.</li>
        </ul>
    </div>
</div>