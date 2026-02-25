<aside class="w-full lg:w-64 flex-shrink-0">
    <div class="sticky top-24 space-y-8">
        
        {{-- Primary Navigation --}}
        <nav class="space-y-1">
            <x-nav-link :href="route('home')" :active="request()->routeIs('home')" icon="home">
                Feed
            </x-nav-link>

            <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*')" icon="pencil">
                Explore Posts
            </x-nav-link>

            <x-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')" icon="tag">
                Tags
            </x-nav-link>
        </nav>

        {{-- Personal Section (Only for Auth) --}}
        @auth
            <div>
                <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">
                    Personal
                </h3>
                <nav class="space-y-1">
                    <x-nav-link :href="route('profile.show', auth()->user()->username)" 
                                :active="request()->routeIs('profile.show') && request()->route('user')->username === auth()->user()->username" 
                                icon="user">
                        My Profile
                    </x-nav-link>

                    <x-nav-link :href="route('profile.reputation', auth()->user()->username)" 
                                :active="request()->routeIs('profile.reputation') && request()->route('user')->username === auth()->user()->username" 
                                icon="chart">
                        Reputation
                    </x-nav-link>

                    <x-nav-link :href="route('notifications.index')" 
                                :active="request()->routeIs('notifications.*')" 
                                icon="bell">
                        Notifications
                    </x-nav-link>

                    {{-- Special button visible only to Admins and Moderators --}}
                    @if(auth()->user()->canModerate())
                        <div class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                            <x-nav-link :href="route('admin.dashboard')" 
                                        :active="request()->routeIs('admin.*')" 
                                        icon="lock" 
                                        class="bg-gray-900 text-white hover:bg-black dark:bg-white dark:text-gray-900 dark:hover:bg-gray-200">
                                Admin Panel
                            </x-nav-link>
                        </div>
                    @endif
                </nav>
            </div>
        @endauth

        {{-- Recommended Tags Widget --}}
        @if(isset($recommendedTags) && $recommendedTags->count() > 0)
        <div class="bg-white dark:bg-gray-800/40 rounded-2xl p-5 border border-gray-100 dark:border-gray-700/50 shadow-sm">
            <h3 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <x-icons.tag class="w-4 h-4 text-indigo-500" />
                Recommended
            </h3>

            <div class="space-y-4">
                @foreach($recommendedTags as $recTag)
                    <div class="flex items-center justify-between group" 
                         x-data="{ 
                            following: false,
                            toggle() {
                                this.following = true;
                                fetch('{{ route('tags.follow', $recTag) }}', {
                                    method: 'POST',
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                });
                            }
                         }"
                         x-show="!following"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95">
                        
                        <div class="min-w-0">
                            <a href="{{ route('tags.show', $recTag->slug) }}" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600 truncate">
                                #{{ $recTag->name }}
                            </a>
                            <span class="text-[10px] text-gray-400 font-medium">{{ $recTag->posts_count }} posts</span>
                        </div>

                        <button @click="toggle()" class="p-1.5 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-400 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Community Widget --}}
        <div class="bg-white dark:bg-gray-800/40 rounded-2xl p-4 border border-gray-100 dark:border-gray-700/50 shadow-sm">
            <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-2">
                <span class="flex h-2 w-2 rounded-full bg-indigo-500"></span>
                Community Goal
            </h4>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mb-2">
                <div class="bg-indigo-600 h-1.5 rounded-full transition-all duration-1000" style="width: 65%"></div>
            </div>
            <p class="text-[11px] leading-relaxed text-gray-500 dark:text-gray-400">
                <span class="font-bold text-gray-700 dark:text-gray-200">650/1000</span> posts this month to unlock site-wide double reputation!
            </p>
        </div>

    </div>
</aside>