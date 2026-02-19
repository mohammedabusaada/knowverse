<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'KnowVerse') }}</title>

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    
    {{-- Dark Mode Head Script --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- PrismJS for Code Highlighting --}}
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism.min.css" rel="stylesheet" id="prism-light">
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css" rel="stylesheet" id="prism-dark" disabled>
    <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js" defer></script>
</head>

<body class="font-sans antialiased min-h-screen bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    
    {{-- Navigation Bar Component --}}
    <x-nav-bar />

    {{-- Reputation Toast Notification --}}
    @if(session('reputation_delta'))
        <x-toast>+{{ session('reputation_delta') }} reputation</x-toast>
    @endif

    {{-- Layout Wrapper --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-10 py-10">
            
            {{-- Left Sidebar Column --}}
            {{-- Hidden on very small screens, or you can adjust it to be a bottom-nav on mobile later --}}
            <aside class="w-full lg:w-64 flex-shrink-0">
                @include('layouts.includes.sidebar')
            </aside>

            {{-- Main Content Column --}}
            <main class="flex-1 min-w-0">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('alpine:init', () => {
            // Prism Dark Mode Switcher
            Alpine.effect(() => {
                const isDark = document.documentElement.classList.contains('dark');
                const lightPrism = document.getElementById('prism-light');
                const darkPrism = document.getElementById('prism-dark');
                if (lightPrism && darkPrism) {
                    lightPrism.disabled = isDark;
                    darkPrism.disabled = !isDark;
                }
            });
        });
    </script>
</body>
</html>