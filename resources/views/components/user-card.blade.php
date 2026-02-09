@props(['user'])

@php
    $badge = null;
    if (auth()->check()) {
        if (auth()->id() === $user->id) {
            $badge = 'You';
        } elseif ($user->isFollowedBy(auth()->user())) {
            $badge = 'Follows you';
        }
    }
@endphp

<div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:border-indigo-500/50 transition-colors">
    <div class="flex items-center gap-3">
        {{-- Avatar with fallback check --}}
        <img src="{{ $user->profile_picture_url }}" 
             alt="{{ $user->display_name }}"
             class="w-12 h-12 rounded-full object-cover border border-gray-100 dark:border-gray-700">
        
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.show', $user->username) }}" class="font-bold text-gray-900 dark:text-white hover:text-indigo-600 transition-colors">
                    {{ $user->display_name }}
                </a>
                
                @if($badge)
                    <span class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] px-2 py-0.5 rounded-md font-bold uppercase tracking-wider">
                        {{ $badge }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500">{{ '@' . $user->username }}</p>
        </div>
    </div>
    
    <div class="flex items-center gap-2">
        @if(auth()->check() && auth()->id() !== $user->id)
            <x-follow-button :user="$user" />
        @endif
    </div>
</div>