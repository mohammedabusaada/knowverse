<nav

    x-data="{ mobileOpen: false }"
    class="sticky top-0 z-40 bg-white dark:bg-black text-black dark:text-white backdrop-blur border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">

    x-data="{
        mobileOpen: false,
        darkMode: localStorage.getItem('theme') === 'dark'
    }"
    x-init="document.documentElement.classList.toggle('dark', darkMode)"
    class="sticky top-0 z-40
           bg-white/80 dark:bg-gray-900/80
           backdrop-blur
           border-b border-gray-200 dark:border-gray-700"

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">


            <a href="/" class="flex items-center space-x-1 group">
                <span class="text-3xl font-extrabold tracking-tight text-black dark:text-white transition duration-300">
                    Know
                </span>
                <span class="text-3xl font-extrabold tracking-tight text-indigo-600 transition duration-300">
                    Verse
                </span>
            </a>
            <!-- Logo -->
            <a href="/" class="flex items-center">
                <span class="text-2xl font-extrabold tracking-tight
                             text-black dark:text-white">
                    KnowVerse
                </span>
            </a>

            <!-- Desktop Search -->

            <div class="hidden md:flex flex-1 justify-center px-8">
                <x-search-bar placeholder="Search knowledge, posts, tags..." />
            </div>

            <div class="hidden md:flex items-center gap-4">

                <button

                    @click="darkMode = !darkMode"
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                    aria-label="Toggle Dark Mode">

                    @click="
                        darkMode = !darkMode;
                        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                        document.documentElement.classList.toggle('dark');
                    "
                    class="p-2 rounded-full
                           hover:bg-gray-200 dark:hover:bg-gray-700
                           transition"

                    <template x-if="!darkMode">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 0112.21 3a9 9 0 100 18c4.42 0 8.21-3.05 8.79-7.21z" />
                        </svg>
                    </template>

                    <template x-if="darkMode">
                        <svg class="w-6 h-6 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m16.364 6.364l-.707-.707M6.343 6.343L5.636 5.636m12.728 0l-.707.707M6.343 17.657l-.707.707" />
                            <circle cx="12" cy="12" r="4" stroke-width="2" />

                            <path stroke-width="2" d="M21 12.79A9 9 0 0112.21 3a9 9 0 100 18c4.42 0 8.21-3.05 8.79-7.21z"/>

                        </svg>
                    </template>
                </button>


                <!-- Auth User -->

                @auth
                <x-notification-dropdown />
                @endauth


                @auth
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex focus:outline-none">
                        <img src="{{ auth()->user()->profile_picture_url }}"
                            class="w-9 h-9 rounded-full object-cover border border-gray-300 dark:border-gray-600 shadow-sm">

                    <button @click="open = !open">
                        <img src="{{ auth()->user()->profile_picture_url }}"
                             class="w-9 h-9 rounded-full object-cover
                                    border border-gray-300 dark:border-gray-600
                                    shadow-sm">
                    </button>

                    <div
                        x-show="open"
                        @click.away="open = false"

                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        class="absolute right-0 mt-3 w-48 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-2">
                        <a href="{{ route('profiles.show', auth()->user()->username) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">My Profile</a>
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Edit Profile</a>
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Dashboard</a>
                        <hr class="my-1 border-gray-200 dark:border-gray-700">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Logout</button>

                        x-transition
                        class="absolute right-0 mt-3 w-48
                               bg-white dark:bg-gray-800
                               border border-gray-200 dark:border-gray-700
                               rounded-xl shadow-lg py-2"
                    >
                        <a href="{{ route('profiles.show', auth()->user()->username) }}"
                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                            My Profile
                        </a>

                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Edit Profile
                        </a>

                        <a href="{{ route('dashboard') }}"
                           class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700">
                            Dashboard
                        </a>

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="w-full text-left px-4 py-2
                                           text-red-600 hover:bg-red-50
                                           dark:hover:bg-red-900/20">
                                Logout
                            </button>

                        </form>
                    </div>
                </div>
                @endauth

                <!-- Guest -->
                @guest

                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium hover:text-indigo-600 transition">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium bg-black dark:bg-white text-white dark:text-black rounded-lg hover:opacity-90 transition">Register</a>
                </div>
                @endguest
            </div>

            <div class="md:hidden flex items-center gap-2">
                <button @click="darkMode = !darkMode" class="p-2">
                    <span x-show="!darkMode">🌙</span>
                    <span x-show="darkMode">☀️</span>
                </button>
                <button @click="mobileOpen = !mobileOpen" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                    <a href="{{ route('login') }}"
                       class="text-gray-700 dark:text-gray-200 hover:underline">
                        Login
                    </a>

                    <a href="{{ route('register') }}"
                       class="px-4 py-2 rounded-lg
                              bg-black text-white
                              hover:bg-gray-800 transition">
                        Register
                    </a>
                @endguest
            </div>

            <!-- Mobile Button -->
            <div class="md:hidden">
                <button @click="mobileOpen = !mobileOpen"
                        class="p-2 rounded-md
                               hover:bg-gray-200 dark:hover:bg-gray-700">


                </button>
            </div>
        </div>
    </div>


    <div x-show="mobileOpen" x-transition class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
        <div class="px-4 py-4 space-y-4">
            <x-search-bar placeholder="Search..." />
            @auth
            <a href="{{ route('dashboard') }}" class="block">Dashboard</a>
            <form action="{{ route('logout') }}" method="POST">@csrf<button class="text-red-600">Logout</button></form>
            @endauth

    <!-- Mobile Menu -->
    <div x-show="mobileOpen"
         x-transition
         class="md:hidden bg-white dark:bg-gray-900
                border-t border-gray-200 dark:border-gray-700">

        <div class="px-4 py-4 space-y-4">

            <x-search-bar placeholder="Search..." />

            @auth
                <a href="{{ route('profiles.show', auth()->user()->username) }}" class="block">My Profile</a>
                <a href="{{ route('profile.edit') }}" class="block">Edit Profile</a>
                <a href="{{ route('dashboard') }}" class="block">Dashboard</a>

                <button
                    @click="
                        darkMode = !darkMode;
                        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                        document.documentElement.classList.toggle('dark');
                    "
                    class="block w-full text-left">
                    <span x-show="!darkMode">🌙 Dark Mode</span>
                    <span x-show="darkMode">☀️ Light Mode</span>
                </button>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="text-red-600">Logout</button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="block">Login</a>
                <a href="{{ route('register') }}" class="block">Register</a>
            @endguest
        </div>
    </div>
</nav>
