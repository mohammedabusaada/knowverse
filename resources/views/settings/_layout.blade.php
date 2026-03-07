@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 animate-[fadeUp_0.8s_ease_both]">
    
    {{-- Configuration Navigation Routing --}}
    <nav class="flex flex-wrap gap-6 border-b border-rule mb-10 pb-2 font-mono text-[10px] uppercase tracking-widest">
        <a href="{{ route('profile.show', auth()->user()->username) }}" class="text-muted hover:text-ink transition-colors flex items-center gap-1.5 mr-auto">
            &larr; Back to Profile
        </a>

        <a href="{{ route('profile.edit') }}" class="transition-colors pb-2 border-b-2 {{ request()->routeIs('profile.edit') ? 'border-ink text-ink font-bold' : 'border-transparent text-muted hover:text-ink hover:border-ink/50' }}">
            Public Profile
        </a>

        <a href="{{ route('settings.notifications') }}" class="transition-colors pb-2 border-b-2 {{ request()->routeIs('settings.notifications') ? 'border-ink text-ink font-bold' : 'border-transparent text-muted hover:text-ink hover:border-ink/50' }}">
            Notifications
        </a>

        <a href="{{ route('settings.security') }}" class="transition-colors pb-2 border-b-2 {{ request()->routeIs('settings.security') ? 'border-ink text-ink font-bold' : 'border-transparent text-muted hover:text-ink hover:border-ink/50' }}">
            Security
        </a>
    </nav>

    {{-- Dynamic Settings Module Injection --}}
    <main class="bg-paper">
        @yield('settings-content')
    </main>

</div>
@endsection