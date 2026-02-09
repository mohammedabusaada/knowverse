@extends('layouts.app')

@section('content')
{{-- Added min-h to ensure the layout feels full-screen --}}
<div class="flex flex-col md:flex-row gap-10 min-h-[70vh]">
    
    {{-- Settings Sidebar --}}
    <aside class="w-full md:w-64 flex-shrink-0">
        {{-- Sticky ensures it stays visible as you scroll long forms --}}
        <div class="sticky top-24 space-y-8">
            <div>
                <h2 class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest px-3 mb-4">
                    Settings
                </h2>
                
                <nav class="space-y-1">
                    <x-settings-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.edit')">
                        <x-icons.user class="w-5 h-5" />
                        <span>Public Profile</span>
                    </x-settings-nav-link>

                    <x-settings-nav-link href="{{ route('settings.notifications') }}" :active="request()->routeIs('settings.notifications')">
                        <x-icons.bell class="w-5 h-5" />
                        <span>Notifications</span>
                    </x-settings-nav-link>

                    <x-settings-nav-link href="{{ route('settings.security') }}" :active="request()->routeIs('settings.security')">
                        <x-icons.lock class="w-5 h-5" />
                        <span>Security</span>
                    </x-settings-nav-link>
                </nav>
            </div>

            <div class="pt-6 border-t border-gray-200 dark:border-gray-800">
                <a href="{{ route('profile.show', auth()->user()->username) }}" 
                   class="group flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-500 hover:text-indigo-600 dark:hover:text-white transition-colors">
                    <x-icons.arrow-left class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                    Back to Profile
                </a>
            </div>
        </div>
    </aside>

    {{-- Settings Content Area --}}
    <main class="flex-1">
        {{-- The 'bg-white' box now holds your actual settings forms --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm min-h-[500px]">
            <div class="p-6 md:p-10">
                @yield('settings-content')
            </div>
        </div>
    </main>
</div>
@endsection