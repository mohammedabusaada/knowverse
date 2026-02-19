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
            
            {{-- Updated: Replaced text with Application Logo Component --}}
            <a href="/" class="flex items-center group shrink-0">
    <x-application-logo class="h-8 w-auto transition-transform duration-300 group-hover:scale-105" />
</a>

            <div class="hidden md:flex flex-1 justify-center px-8">
                <x-search-bar placeholder="Search knowledge..." />
            </div>

            <div class="hidden md:flex items-center gap-2 shrink-0">
                <button 
                    @click="darkMode = !darkMode"
                    class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 dark:text-gray-400 transition-all"
                >
                    <x-icons.sun x-show="darkMode" class="w-5 h-5 text-yellow-400" x-cloak />
                    <x-icons.moon x-show="!darkMode" class="w-5 h-5" x-cloak />
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
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-3 w-56 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-xl py-2 z-50 overflow-hidden"
                            x-cloak
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
                                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
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
                        <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" x-cloak />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="mobileOpen" x-transition x-cloak class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 px-4 py-6 space-y-4 shadow-lg">
        <div class="mb-4">
            <x-search-bar placeholder="Search..." />
        </div>

        @auth
            <div class="flex items-center gap-3 mb-6 px-2">
                <x-user-avatar :user="auth()->user()" size="md" />
                <div class="min-w-0">
                    <p class="font-bold dark:text-white truncate">{{ auth()->user()->full_name }}</p>
                    <p class="text-sm text-gray-500 truncate">@{{ auth()->user()->username }}</p>
                </div>
            </div>
            
            <div class="space-y-1">
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex items-center gap-3 px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                    <x-icons.user class="w-5 h-5 text-gray-400" /> My Profile
                </a>
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                    <x-icons.chart class="w-5 h-5 text-gray-400" /> Dashboard
                </a>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 text-base font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                    <x-icons.pencil class="w-5 h-5 text-gray-400" /> Settings
                </a>
            </div>

            <hr class="my-4 border-gray-200 dark:border-gray-700">

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-base font-medium text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                    <x-icons.logout class="w-5 h-5" /> Logout
                </button>
            </form>
        @else
            <div class="space-y-3">
                <a href="{{ route('login') }}" class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-800 rounded-xl">Login</a>
                <a href="{{ route('register') }}" class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl shadow-sm">Sign Up</a>
            </div>
        @endauth

        <hr class="my-4 border-gray-200 dark:border-gray-700">

        {{-- Updated: Replaced emojis with SVG icons for Dark Mode toggle --}}
        <button @click="darkMode = !darkMode" class="flex items-center gap-3 px-3 py-2 w-full text-base font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
            <template x-if="darkMode">
                <div class="flex items-center gap-3">
                    <x-icons.sun class="w-5 h-5 text-yellow-400" /> Light Mode
                </div>
            </template>
            <template x-if="!darkMode">
                <div class="flex items-center gap-3">
                    <x-icons.moon class="w-5 h-5 text-gray-500" /> Dark Mode
                </div>
            </template>
        </button>
    </div>
</nav>