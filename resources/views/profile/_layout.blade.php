<div class="max-w-7xl mx-auto px-6 py-10">
    {{-- Profile Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-6 md:gap-8 mb-10">
        <div class="relative group">
            <img src="{{ $user->profile_picture_url }}"
                class="w-28 h-28 md:w-32 md:h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-black/40 transition-transform duration-300 group-hover:scale-105"
                alt="{{ $user->display_name }}" />
            <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-500/20 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
        </div>

        <div class="flex-1">
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ $user->display_name }}
            </h1>

            <div class="mt-1.5 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium text-gray-700 dark:text-gray-300">{{'@'. $user->username }}</span>
                <span class="hidden sm:inline text-gray-400">·</span>
                <span>Joined {{ $user->joined_date }}</span>
            </div>

            <div class="mt-3 flex gap-6 text-sm font-medium">
                <div class="flex items-center gap-1.5">
                    <span class="text-amber-600 dark:text-amber-400">{{ number_format($user->reputation_points) }}</span>
                    <span class="text-gray-500 dark:text-gray-400">reputation</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-indigo-600 dark:text-indigo-400">{{ $user->posts_count ?? $user->posts()->count() }}</span>
                    <span class="text-gray-500 dark:text-gray-400">posts</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="self-start sm:self-center mt-4 sm:mt-0 flex items-center gap-3">
            @auth
                @if(auth()->id() === $user->id)
                    <x-button href="{{ route('profile.edit') }}" secondary>Edit Profile</x-button>
                @else
                    <x-follow-button :user="$user" />
                    
                    {{-- Report user button (Dropdown) --}}
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
    <div class="border-b border-gray-200/80 dark:border-gray-700/70 mb-8">
        <nav class="flex gap-8 -mb-px overflow-x-auto pb-1 scrollbar-hide">
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