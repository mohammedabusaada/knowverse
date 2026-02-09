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
    class="sticky top-0 z-50 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 transition-all duration-300"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            
            <a href="/" class="flex items-center space-x-1 group">
                <span class="text-2xl font-extrabold tracking-tight text-black dark:text-white">
                    Know<span class="text-indigo-600">Verse</span>
                </span>
            </a>

            <div class="hidden md:flex flex-1 justify-center px-8">
                <x-search-bar placeholder="Search knowledge..." />
            </div>

            <div class="hidden md:flex items-center gap-2">
                <button 
                    @click="darkMode = !darkMode"
                    class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all"
                >
                    <x-icons.sun x-show="darkMode" class="w-5 h-5 text-yellow-400" />
                    <x-icons.moon x-show="!darkMode" class="w-5 h-5" />
                </button>

                @auth
                    <x-notification-dropdown />

                    <div x-data="{ open: false }" class="relative ml-2">
                        <button @click="open = !open" class="flex items-center focus:outline-none group">
                            <x-user-avatar :user="auth()->user()" size="sm" class="border-2 group-hover:border-indigo-500 transition-all" />
                        </button>

                        <div 
                            x-show="open" 
                            @click.away="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl py-2 z-50 overflow-hidden"
                        >
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 mb-1">
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</p>
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->username }}</p>
                            </div>

                            <x-dropdown-link :href="route('profile.show', auth()->user()->username)" icon="user">My Profile</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')" icon="pencil">Edit Profile</x-dropdown-link>
                            <x-dropdown-link :href="route('settings.notifications')" icon="bell">Notifications</x-dropdown-link>
                            <x-dropdown-link :href="route('dashboard')" icon="chart">Dashboard</x-dropdown-link>

                            <hr class="my-1 border-gray-100 dark:border-gray-700">
                            
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <x-icons.logout class="w-4 h-4" />
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                @guest
                    <div class="flex items-center gap-3">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 dark:text-gray-300 hover:text-indigo-600 transition">Login</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-bold bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 shadow-md transition">Sign Up</a>
                    </div>
                @endguest
            </div>

            <div class="md:hidden flex items-center">
                <button @click="mobileOpen = !mobileOpen" class="p-2 text-gray-600 dark:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-transition class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-4 py-6 space-y-4">
        @auth
            <div class="flex items-center gap-3 mb-6 px-2">
                <x-user-avatar :user="auth()->user()" size="md" />
                <div>
                    <p class="font-bold dark:text-white">{{ auth()->user()->full_name }}</p>
                    <p class="text-sm text-gray-500">@<span>{{ auth()->user()->username }}</span></p>
                </div>
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-2 py-1 text-lg font-medium dark:text-gray-200">Settings</a>
            <a href="{{ route('dashboard') }}" class="block px-2 py-1 text-lg font-medium dark:text-gray-200">Dashboard</a>
        @endauth
        <button @click="darkMode = !darkMode" class="flex items-center gap-2 px-2 py-1 text-lg font-medium dark:text-gray-200">
            <span x-text="darkMode ? '☀️ Light Mode' : '🌙 Dark Mode'"></span>
        </button>
    </div>
</nav>