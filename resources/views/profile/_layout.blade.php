<div class="max-w-6xl mx-auto px-4 py-8">

    {{-- Profile Header --}}
    <div class="flex items-center gap-6 mb-8">
        <img
            src="{{ $user->profile_picture_url }}"
            class="w-24 h-24 rounded-full border dark:border-gray-700"
            alt="{{ $user->display_name }}"
        />

        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $user->display_name }}
            </h1>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ '@' . $user->username }} · Joined {{ $user->joined_date }}
            </p>

            <div class="flex gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                <span>{{ $user->reputation_points }} reputation</span>
                <span>{{ $user->posts()->count() }} posts</span>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b dark:border-gray-700 mb-6">
        <nav class="flex gap-6">

            <a href="{{ route('profiles.show', $user) }}"
               class="pb-3 font-medium border-b-2 transition
               {{ request()->routeIs('profiles.show')
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Profile
            </a>

            <a href="{{ route('activity.index', $user) }}"
               class="pb-3 font-medium border-b-2 transition
               {{ request()->routeIs('activity.index')
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Activity
            </a>

            <a href="{{ route('reputation.index', $user) }}"
               class="pb-3 font-medium border-b-2 transition
               {{ request()->routeIs('reputation.index')
                    ? 'border-blue-600 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                Reputation
            </a>

        </nav>
    </div>

    {{-- Page Content --}}
    <div>
    @yield('profile-content') 
</div>

</div>
