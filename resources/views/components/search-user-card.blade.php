@props([
    'user',
])

<a href="{{ route('profiles.show', $user->username) }}"
   class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800
          border border-gray-200 dark:border-gray-700
          rounded-xl hover:shadow-md transition">

    {{-- Avatar --}}
    <img
        src="{{ $user->profile_picture_url }}"
        alt="{{ $user->display_name }}"
        class="w-12 h-12 rounded-full object-cover shrink-0">

    {{-- User Info --}}
    <div class="flex-1 min-w-0">
        <p class="font-semibold dark:text-white truncate">
            {{ $user->display_name }}
        </p>

        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
            {{ '@' . $user->username }}
        </p>
    </div>
</a>
