<div class="sticky top-24 space-y-10">
    
    {{-- Primary Navigation --}}
    <div>
        <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2">
            Navigation
        </h3>
        <nav class="space-y-1">
            <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                Home
            </x-nav-link>

            <x-nav-link :href="route('posts.index')" :active="request()->routeIs('posts.*') && !request()->routeIs('posts.saved')">
                Discussions
            </x-nav-link>

            <x-nav-link :href="route('tags.index')" :active="request()->routeIs('tags.*')">
                Tags
            </x-nav-link>
        </nav>
    </div>

    {{-- Personal Section --}}
    @auth
        <div>
            <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2">
                Personal
            </h3>
            <nav class="space-y-1">
                <x-nav-link :href="route('profile.show', auth()->user()->username)" 
                            :active="request()->routeIs('profile.show') && request()->route('user')->username === auth()->user()->username">
                    Profile
                </x-nav-link>

                <x-nav-link :href="route('posts.saved')" 
                            :active="request()->routeIs('posts.saved')">
                    Saved Posts
                </x-nav-link>

                <x-nav-link :href="route('profile.reputation', auth()->user()->username)" 
                            :active="request()->routeIs('profile.reputation') && request()->route('user')->username === auth()->user()->username">
                    Reputation
                </x-nav-link>

                <x-nav-link :href="route('notifications.index')" 
                            :active="request()->routeIs('notifications.*')">
                    Notifications
                </x-nav-link>
            </nav>
        </div>
    @endauth

    {{-- Recommended Tags Widget --}}
    @if(isset($recommendedTags) && $recommendedTags->count() > 0)
        <div>
            <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2">
                Recommended
            </h3>
            <div class="space-y-2">
                @foreach($recommendedTags as $recTag)
                    <div class="flex items-baseline justify-between group" 
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
                         x-show="!following">
                        
                        <a href="{{ route('tags.show', $recTag->slug) }}" class="font-serif text-[15px] text-ink hover:text-accent transition-colors truncate">
                            {{ strtolower($recTag->name) }}
                        </a>

                        <button @click="toggle()" class="font-mono text-[9px] uppercase tracking-widest text-muted hover:text-accent transition-colors shrink-0 ml-2 focus:outline-none">
                            Follow
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>