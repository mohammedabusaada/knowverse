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
                class="min-w-[140px] flex items-center justify-center gap-2 px-6 py-2.5 rounded-full text-sm font-semibold tracking-wide uppercase transition-all duration-300 shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed border-2 transform active:scale-[0.98] {{ $isFollowing 
                    ? 'bg-white text-gray-700 border-gray-300 hover:bg-red-50 hover:text-red-600 hover:border-red-200' 
                    : 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white border-transparent hover:from-blue-700 hover:to-indigo-700' }}"
            >
                {{-- Spinner --}}
                <svg x-show="loading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-30" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-80" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                </svg>

                <span x-text="loading ? 'Processing...' : ('{{ $isFollowing ? 'Following' : 'Follow' }}')"></span>
            </button>
        </form>
    </div>
@else
    <a href="{{ route('login') }}" class="min-w-[140px] flex items-center justify-center px-6 py-2.5 rounded-full text-sm font-semibold tracking-wide uppercase bg-gray-100 text-gray-600 border-2 border-gray-200">
        Follow
    </a>
@endauth