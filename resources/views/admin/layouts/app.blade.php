<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('theme') === 'dark', sidebarOpen: false }"
      x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - {{ config('app.name', 'KnowVerse') }}</title>
    
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('admin.layouts.sidebar')

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        {{-- Topbar --}}
        <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-10">
            <div class="flex items-center gap-4">
                {{-- Mobile menu toggle button --}}
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <h2 class="text-xl font-bold tracking-tight text-gray-800 dark:text-white truncate">
                    @yield('header', 'Dashboard')
                </h2>
            </div>

            <div class="flex items-center gap-4">
                {{-- Dark mode button --}}
                <button @click="darkMode = !darkMode" class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 transition">
                    <x-icons.sun x-show="darkMode" class="w-5 h-5 text-yellow-400" x-cloak />
                    <x-icons.moon x-show="!darkMode" class="w-5 h-5" x-cloak />
                </button>
                
                {{-- Admin Profile Picture --}}
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex items-center gap-2 border-l border-gray-200 dark:border-gray-700 pl-4 ml-2">
                    <x-user-avatar :user="auth()->user()" size="sm" class="ring-2 ring-gray-100 dark:ring-gray-800" />
                </a>
            </div>
        </header>

        {{-- Scrollable Content Area --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 relative">
            @yield('content')
        </main>
    </div>

</body>
</html>