<nav 
    x-data="{
        mobileOpen: false,
        darkMode: localStorage.getItem('theme') === 'dark'
    }"
    x-init="
        document.documentElement.classList.toggle('dark', darkMode);
    "
    class="sticky top-0 z-40 bg-white/90 dark:bg-gray-900/90 backdrop-blur border-b border-gray-200 dark:border-gray-700"
>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex justify-between items-center h-16">

            <!-- Left: Logo -->
            <a href="/" class="flex items-center gap-2">
                <span class="text-2xl font-extrabold text-blue-600 dark:text-blue-400">
                    Knowverse
                </span>
            </a>

            <!-- Center: Desktop Search -->
            <div class="hidden md:flex flex-1 justify-center px-6">
                <x-search-bar placeholder="Search knowledge, posts, tags..." />
            </div>

            <!-- Right Section -->
            <div class="hidden md:flex items-center gap-4">

                <!-- Dark Mode Toggle -->
                <button
                    @click="
                        darkMode = !darkMode;
                        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                        document.documentElement.classList.toggle('dark');
                    "
                    class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                >
                    <template x-if="!darkMode">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m16.364 6.364l-.707-.707M6.343 6.343L5.636 5.636m12.728 0l-.707.707M6.343 17.657l-.707.707" />
                            <circle cx="12" cy="12" r="4" stroke-width="2"/>
                        </svg>
                    </template>

                    <template x-if="darkMode">
                        <svg class="w-6 h-6 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M21 12.79A9 9 0 0112.21 3c-.49 0-.97.04-1.44.12a9 9 0 1010.23 10.23c.08-.47.12-.95.12-1.44z"/>
                        </svg>
                    </template>
                </button>

                <!-- Authenticated User -->
                @auth
                <div x-data="{ open: false }" class="relative">

                    <button @click="open = !open" class="flex items-center">
                        <img src="{{ auth()->user()->profile_picture_url }}"
                            class="w-9 h-9 rounded-full object-cover border shadow">
                    </button>

                    <!-- Dropdown -->
                    <div
                        x-show="open"
                        @click.away="open = false"
                        x-transition
                        class="absolute right-0 mt-3 w-48 bg-white dark:bg-gray-800 border border-gray-200
                               dark:border-gray-700 rounded-xl shadow-lg py-2 z-50"
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
                            <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                Logout
                            </button>
                        </form>
                    </div>

                </div>
                @endauth

                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 dark:text-gray-200 hover:underline">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                       class="px-3 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                        Register
                    </a>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex items-center">
                <button @click="mobileOpen = !mobileOpen"
                        class="p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <span class="text-3xl">☰</span>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen"
         x-transition
         class="md:hidden border-t bg-white dark:bg-gray-900 dark:border-gray-700">

        <!-- Search -->
        <div class="px-4 py-3">
            <x-search-bar placeholder="Search..." />
        </div>

        <!-- Links -->
        <div class="px-4 py-3 space-y-3">

            @auth
                <a href="{{ route('profiles.show', auth()->user()->username) }}"
                   class="block py-2 text-gray-700 dark:text-gray-200">
                   My Profile
                </a>

                <a href="{{ route('profile.edit') }}"
                   class="block py-2 text-gray-700 dark:text-gray-200">
                   Edit Profile
                </a>

                <a href="{{ route('dashboard') }}"
                   class="block py-2 text-gray-700 dark:text-gray-200">
                   Dashboard
                </a>

                <!-- Dark Mode -->
                <button
                    @click="
                        darkMode = !darkMode;
                        localStorage.setItem('theme', darkMode ? 'dark' : 'light');
                        document.documentElement.classList.toggle('dark');
                    "
                    class="block w-full text-left py-2 text-gray-700 dark:text-gray-200"
                >
                    <span x-show="!darkMode">🌙 Enable Dark Mode</span>
                    <span x-show="darkMode">☀️ Disable Dark Mode</span>
                </button>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="block w-full text-left py-2 text-red-600">
                        Logout
                    </button>
                </form>

            @endauth

            @guest
                <a href="{{ route('login') }}" class="block py-2">Login</a>
                <a href="{{ route('register') }}" class="block py-2">Register</a>
            @endguest

        </div>
    </div>

</nav>
