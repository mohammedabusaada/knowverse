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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- PrismJS for Code Highlighting --}}
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism.min.css" rel="stylesheet" id="prism-light">
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css" rel="stylesheet" id="prism-dark" disabled>
    <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js" defer></script>
</head>

<body class="antialiased min-h-screen selection:bg-accent selection:text-paper">
    
    {{-- Navigation Bar Component --}}
    <x-nav-bar />

    {{-- Reputation Toast Notification --}}
    @if(session('reputation_delta'))
        <x-toast>+{{ session('reputation_delta') }} reputation</x-toast>
    @endif

    {{-- Layout Wrapper --}}
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-10 py-10">
            
            {{-- Left Sidebar Column --}}
            <aside class="w-full lg:w-56 flex-shrink-0">
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