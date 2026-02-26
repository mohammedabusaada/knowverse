@props(['user'])

@auth
    @php
        $isFollowing = auth()->user()->following()->where('followed_id', $user->id)->exists();
    @endphp

    <div x-data="{ loading: false }">
        <form action="{{ route('users.follow', $user->username) }}" 
              method="POST" 
              @submit="loading = true">
            @csrf
            <button
                type="submit"
                :disabled="loading"
                class="min-w-[120px] flex items-center justify-center gap-2 px-5 py-2 rounded-lg text-xs font-bold tracking-widest uppercase transition-all duration-200 border-2 disabled:opacity-50 disabled:cursor-not-allowed {{ $isFollowing 
                    ? 'bg-white text-black border-gray-200 hover:border-black dark:bg-black dark:text-white dark:border-gray-800 dark:hover:border-white' 
                    : 'bg-black text-white border-black hover:bg-gray-800 dark:bg-white dark:text-black dark:border-white dark:hover:bg-gray-200 shadow-md' }}"
            >
                <svg x-show="loading" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" x-cloak>
                    <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>
                <span x-text="loading ? '...' : ('{{ $isFollowing ? 'Following' : 'Follow' }}')"></span>
            </button>
        </form>
    </div>
@else
    <a href="{{ route('login') }}" class="min-w-[120px] flex items-center justify-center px-5 py-2 rounded-lg text-xs font-bold tracking-widest uppercase bg-black text-white dark:bg-white dark:text-black transition-all shadow-md hover:scale-105">
        Follow
    </a>
@endauth