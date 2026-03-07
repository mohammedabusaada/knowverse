<nav 
    x-data="{ 
        mobileOpen: false, 
        darkMode: localStorage.getItem('theme') === 'dark' 
    }" 
    x-init="
        $watch('darkMode', val => {
            localStorage.setItem('theme', val ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', val);
        })
    "
    class="sticky top-0 z-50 bg-paper/95 backdrop-blur-md border-b border-rule transition-all duration-300 flex items-center min-h-[56px]"
>
    <div class="w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        
        {{-- Wordmark (Logo) --}}
        <a href="/" class="font-heading font-bold text-base tracking-[0.08em] uppercase text-ink hover:opacity-70 transition-opacity shrink-0">
            KnowVerse
        </a>

        {{-- Center Search --}}
        <div class="hidden md:flex flex-1 justify-center px-12 max-w-2xl">
            <x-search-bar placeholder="Search discussions..." />
        </div>

        {{-- Right Navigation --}}
        <div class="hidden md:flex items-center gap-6 shrink-0">
            
            {{-- Theme Toggle --}}
            <button 
                @click="darkMode = !darkMode"
                class="font-mono text-xs uppercase tracking-[0.1em] text-muted hover:text-ink transition-colors focus:outline-none"
            >
                <span x-show="!darkMode">◑ Dark</span>
                <span x-show="darkMode" x-cloak>◐ Light</span>
            </button>

            @auth
                <x-notification-dropdown />

                <div x-data="{ open: false }" class="relative ml-2">
                    <button @click="open = !open" class="flex items-center focus:outline-none group">
                        <x-user-avatar :user="auth()->user()" size="sm" class="border border-rule group-hover:border-ink transition-colors" />
                    </button>

                    {{-- User Dropdown Menu --}}
                    <div 
                        x-show="open" 
                        @click.away="open = false"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="transform opacity-0 translate-y-1"
                        x-transition:enter-end="transform opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="transform opacity-100 translate-y-0"
                        x-transition:leave-end="transform opacity-0 translate-y-1"
                        class="absolute right-0 mt-3 w-48 bg-paper border border-rule shadow-xl py-1 z-50 rounded-sm"
                        x-cloak
                    >
                        <div class="px-4 py-2 border-b border-rule mb-1">
                            <p class="font-mono text-[10px] text-muted uppercase tracking-[0.15em]">Signed in as</p>
                            <p class="text-sm font-bold text-ink truncate font-heading">{{ auth()->user()->username }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.show', auth()->user()->username)">My Profile</x-dropdown-link>
                        <x-dropdown-link :href="route('profile.edit')">Settings</x-dropdown-link>
                        
                        @if(auth()->user()->canModerate())
                            <x-dropdown-link :href="route('admin.dashboard')">Admin Panel</x-dropdown-link>
                        @endif

                        <div class="my-1 border-t border-rule"></div>
                        
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-1.5 text-sm font-serif text-accent-warm hover:bg-aged hover:text-accent-warm transition-colors focus:outline-none">
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="font-mono text-xs uppercase tracking-[0.1em] text-muted hover:text-ink transition-colors">Sign in</a>
                <a href="{{ route('register') }}" class="font-mono text-xs uppercase tracking-[0.1em] text-ink border-b border-ink pb-0.5 hover:text-accent transition-colors">Register</a>
            @endauth
        </div>

        {{-- Mobile Menu Toggle --}}
        <div class="md:hidden flex items-center">
            <button @click="mobileOpen = !mobileOpen" class="text-muted hover:text-ink transition-colors focus:outline-none">
                <span class="font-mono text-xs uppercase tracking-widest" x-show="!mobileOpen">Menu</span>
                <span class="font-mono text-xs uppercase tracking-widest" x-show="mobileOpen" x-cloak>Close</span>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Panel --}}
    <div x-show="mobileOpen" x-transition x-cloak class="absolute top-full left-0 w-full md:hidden bg-paper border-b border-rule px-6 py-8 shadow-xl">
        <div class="mb-6">
            <x-search-bar placeholder="Search discussions..." />
        </div>

        @auth
            <div class="mb-6 pb-6 border-b border-rule">
                <p class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-2">Signed in as</p>
                <div class="flex items-center gap-3">
                    <x-user-avatar :user="auth()->user()" size="md" />
                    <div class="min-w-0">
                        <p class="font-heading font-bold text-ink truncate">{{ auth()->user()->display_name }}</p>
                        <p class="font-mono text-xs text-muted truncate">{{ '@' . auth()->user()->username }}</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-3 flex flex-col mb-6">
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="font-serif text-lg text-ink hover:text-accent-warm transition-colors">My Profile</a>
                @if(auth()->user()->canModerate())
                    <a href="{{ route('admin.dashboard') }}" class="font-serif text-lg text-ink hover:text-accent-warm transition-colors">Admin Panel</a>
                @endif
                <a href="{{ route('profile.edit') }}" class="font-serif text-lg text-ink hover:text-accent-warm transition-colors">Settings</a>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="font-mono text-xs uppercase tracking-[0.1em] text-accent-warm hover:opacity-70 transition-opacity focus:outline-none">
                    Sign out
                </button>
            </form>
        @else
            <div class="flex flex-col gap-4">
                <a href="{{ route('login') }}" class="font-serif text-lg text-ink hover:text-accent-warm transition-colors">Sign in</a>
                <a href="{{ route('register') }}" class="font-serif text-lg text-ink hover:text-accent-warm transition-colors">Create Account</a>
            </div>
        @endauth

        <div class="mt-8 pt-6 border-t border-rule">
            <button @click="darkMode = !darkMode" class="font-mono text-xs uppercase tracking-[0.1em] text-muted hover:text-ink transition-colors focus:outline-none">
                <template x-if="darkMode"><span>◐ Switch to Light Mode</span></template>
                <template x-if="!darkMode"><span>◑ Switch to Dark Mode</span></template>
            </button>
        </div>
    </div>
</nav>