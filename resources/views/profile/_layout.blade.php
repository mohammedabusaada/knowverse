<div class="max-w-7xl mx-auto px-6 py-10">
    {{-- Profile Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-6 md:gap-8 mb-10">
        <div class="relative group shrink-0">
            <img src="{{ $user->profile_picture_url }}"
                class="w-28 h-28 md:w-32 md:h-32 rounded-full object-cover border-4 border-black dark:border-white shadow-xl transition-transform duration-300 group-hover:scale-105"
                alt="{{ $user->display_name }}" />
        </div>

        <div class="flex-1 min-w-0">
            <h1 class="text-3xl md:text-4xl font-black text-black dark:text-white tracking-tight flex items-center gap-3">
                <span class="truncate">{{ $user->display_name }}</span>
                
                {{-- Banned Badge --}}
                @if($user->isBanned())
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-black bg-black text-white dark:bg-white dark:text-black uppercase tracking-widest shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Suspended
                    </span>
                @endif
            </h1>

            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-gray-600 dark:text-gray-400 font-medium">
                <span class="font-bold text-gray-900 dark:text-gray-200">{{'@'. $user->username }}</span>
                <span class="hidden sm:inline text-gray-300 dark:text-gray-700">&bull;</span>
                <span>Joined {{ $user->joined_date }}</span>
            </div>

            <div class="mt-4 flex gap-6 text-sm font-black">
                <div class="flex items-center gap-1.5">
                    <span class="text-black dark:text-white">{{ number_format($user->reputation_points) }}</span>
                    <span class="text-gray-500 uppercase tracking-widest text-[10px]">reputation</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-black dark:text-white">{{ $user->posts_count ?? $user->posts()->count() }}</span>
                    <span class="text-gray-500 uppercase tracking-widest text-[10px]">posts</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="self-start sm:self-center mt-4 sm:mt-0 flex items-center gap-3 shrink-0">
            @auth
                @if(auth()->id() === $user->id)
                    <x-button href="{{ route('profile.edit') }}" secondary>Edit Profile</x-button>
                @else
                    <x-follow-button :user="$user" />
                    
                    <x-action-dropdown>
                        <x-report-button type="user" :id="$user->id" />
                    </x-action-dropdown>
                @endif
            @else
                <x-follow-button :user="$user" />
            @endauth
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b-2 border-gray-200 dark:border-gray-800 mb-8">
        <nav class="flex gap-8 -mb-[2px] overflow-x-auto pb-1 scrollbar-hide">
            <x-profile.tab-link :href="route('profile.show', $user->username)" :active="request()->routeIs('profile.show')">
                Profile
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.activity', $user->username)" :active="request()->routeIs('profile.activity')">
                Activity
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.reputation', $user->username)" :active="request()->routeIs('profile.reputation')">
                Reputation
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.following', $user->username)" :active="request()->routeIs('profile.following')">
                Following
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.followers', $user->username)" :active="request()->routeIs('profile.followers')">
                Followers
            </x-profile.tab-link>
        </nav>
    </div>

    <div class="mt-2">
        @yield('profile-content')
    </div>
</div>