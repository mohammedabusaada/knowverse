<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{ darkMode: (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) }"
    x-init="$watch('darkMode', val => localStorage.setItem('theme', val ? 'dark' : 'light'))"
    :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    
    <title>@yield('title', config('app.name', 'KnowVerse'))</title>

    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png" />
    
    {{-- Prevents FOUC (Flash of Unstyled Content) for Dark Mode --}}
    @include('layouts.includes.theme-init')

    {{-- Typography Stack --}}
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="antialiased min-h-screen bg-paper text-ink selection:bg-ink selection:text-paper font-serif flex flex-col">
    
    <a class="skip-link" href="#main-content">Skip to content</a>
    {{-- Global Navigation Bar --}}
    <x-nav-bar />

    {{-- Reputation Toast Notification --}}
    @if(session('reputation_delta'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)" class="fixed bottom-5 right-5 z-[100]">
            <x-toast>+{{ session('reputation_delta') }} reputation</x-toast>
        </div>
    @endif

    {{-- Main Application Container --}}
    <div class="flex-grow w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-10">
        <div class="app-grid">
            
            {{-- Application Sidebar --}}
            <aside class="app-sidebar">
                @include('layouts.includes.sidebar')
            </aside>

            {{-- Dynamic Content Area --}}
            <main id="main-content" tabindex="-1" class="flex-1 min-w-0">
                @yield('content')
            </main>

        </div>
    </div>

    {{-- Global Footer --}}
    @include('layouts.includes.footer')

    @stack('scripts')
</body>
</html>