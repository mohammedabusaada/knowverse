<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: localStorage.getItem('theme') === 'dark' }"
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    
    <title>{{ config('app.name', 'KnowVerse') }}</title>

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    
    {{-- Prevents FOUC (Flash of Unstyled Content) for Dark Mode --}}
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    {{-- Typography Stack --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400;0,500;0,700;1,400;1,500&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    {{-- Syntax Highlighting Themes --}}
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism.min.css" rel="stylesheet" id="prism-light">
    <link href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css" rel="stylesheet" id="prism-dark" disabled>
    <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js" defer></script>
</head>

<body class="antialiased min-h-screen bg-paper text-ink selection:bg-ink selection:text-paper font-serif flex flex-col">
    
    {{-- Global Navigation Bar --}}
    <x-nav-bar />

    {{-- Reputation Toast Notification --}}
    @if(session('reputation_delta'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-5 right-5 z-[100]">
            <x-toast>+{{ session('reputation_delta') }} reputation</x-toast>
        </div>
    @endif

    {{-- Main Application Container --}}
    <div class="flex-grow w-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-10 py-10">
            
            {{-- Application Sidebar --}}
            <aside class="w-full lg:w-56 flex-shrink-0">
                @include('layouts.includes.sidebar')
            </aside>

            {{-- Dynamic Content Area --}}
            <main class="flex-1 min-w-0">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Global Footer --}}
    @include('layouts.includes.footer')

    {{-- Initialization Scripts --}}
    <script>
        document.addEventListener('alpine:init', () => {
            // Synchronize Syntax Highlighting with global theme toggle
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

    @stack('scripts')
</body>
</html>