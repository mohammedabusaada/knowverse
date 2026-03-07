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

    {{-- Academic Typography Stack for consistency --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- Using KnowVerse official colors (bg-paper, text-ink) --}}
<body class="font-serif antialiased bg-paper text-ink flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    @include('admin.layouts.sidebar')

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-aged/10">
        
        {{-- Topbar --}}
        <header class="bg-paper border-b border-rule h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-10 shadow-sm">
            <div class="flex items-center gap-4">
                {{-- Mobile menu toggle button --}}
                <button @click="sidebarOpen = true" class="lg:hidden p-2 text-muted hover:bg-aged rounded-sm transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                
                <h2 class="text-xl font-heading font-bold tracking-tight text-ink truncate">
                    @yield('header', 'Dashboard')
                </h2>
            </div>

            <div class="flex items-center gap-4">
                {{-- Dark mode button --}}
                <button @click="darkMode = !darkMode" class="p-2 rounded-sm hover:bg-aged text-muted transition-colors focus:outline-none">
                    <x-icons.sun x-show="darkMode" class="w-5 h-5 text-accent" x-cloak />
                    <x-icons.moon x-show="!darkMode" class="w-5 h-5" x-cloak />
                </button>
                
                {{-- Admin Profile Picture --}}
                <a href="{{ route('profile.show', auth()->user()->username) }}" class="flex items-center gap-2 border-l border-rule pl-4 ml-2 hover:opacity-80 transition-opacity">
                    <x-user-avatar :user="auth()->user()" size="sm" class="border border-rule" />
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