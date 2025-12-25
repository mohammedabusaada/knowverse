@props(['user', 'date'])

<div class="flex items-center gap-3">
    <a href="{{ route('profiles.show', $user->username) }}">
        <x-user-avatar :src="$user->profile_picture_url" size="md" class="ring-2 ring-gray-100 dark:ring-gray-700" />
    </a>
    <div>
        <div class="flex items-center gap-2">
            <a href="{{ route('profiles.show', $user->username) }}" class="font-bold text-gray-900 dark:text-white hover:text-blue-500">
                {{ $user->display_name }}
            </a>
            @if($user->academic_title)
                <span class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-500 px-1.5 py-0.5 rounded uppercase font-bold tracking-wider">
                    {{ $user->academic_title }}
                </span>
            @endif
        </div>
        <p class="text-xs text-gray-500">
            Published {{ $date->format('M d, Y') }} • {{ $date->diffForHumans() }}
        </p>
    </div>
</div>