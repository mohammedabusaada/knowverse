@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-10 animate-[fadeUp_0.8s_ease_both]">
    {{-- Profile Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-6 md:gap-8 mb-10">
        
        {{-- Profile Portrait --}}
        <div class="relative group shrink-0">
            <x-user-avatar 
                :user="$user" 
                size="2xl" 
                class="border-4 border-ink shadow-xl transition-transform duration-300 group-hover:scale-105" 
            />
        </div>

        <div class="flex-1 min-w-0">
            <h1 class="text-3xl md:text-4xl font-black text-ink tracking-tight flex flex-wrap items-center gap-3">
                <span class="truncate">{{ $user->display_name }}</span>
                
                {{-- Academic Title Display --}}
                @if($user->academic_title)
                    <span class="font-mono text-[10px] uppercase tracking-widest px-2 py-0.5 bg-accent/10 border border-accent/20 text-accent rounded-sm">
                        {{ $user->academic_title }}
                    </span>
                @endif
                
                {{-- Banned Badge --}}
                @if($user->is_banned)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-sm text-xs font-black bg-accent-warm text-paper uppercase tracking-widest shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Suspended
                    </span>
                @endif
            </h1>

            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-muted font-medium">
                <span class="font-bold text-ink font-mono">{{'@'. $user->username }}</span>
                <span class="hidden sm:inline opacity-50">&bull;</span>
                <span class="font-serif italic">Joined {{ $user->created_at->format('M Y') }}</span>
            </div>

            <div class="mt-4 flex gap-6 text-sm font-black font-mono">
                <div class="flex items-center gap-1.5">
                    <span class="text-ink text-base">{{ number_format($user->reputation_points) }}</span>
                    <span class="text-muted uppercase tracking-widest text-[10px]">reputation</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-ink text-base">{{ $user->posts_count ?? $user->posts()->count() }}</span>
                    <span class="text-muted uppercase tracking-widest text-[10px]">posts</span>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="self-start sm:self-center mt-4 sm:mt-0 flex items-center gap-2 shrink-0">
            @auth
                @if(auth()->id() === $user->id)
                    <x-button href="{{ route('profile.edit') }}" secondary class="font-mono text-xs uppercase tracking-widest">Edit Profile</x-button>
                @else
                    <x-follow-button :user="$user" />
                    
                    {{-- Dropdown for secondary actions like Reporting --}}
                    <x-action-dropdown>
                        <x-report-button type="user" :id="$user->id" />
                    </x-action-dropdown>
                @endif
            @else
                <x-follow-button :user="$user" />
            @endauth
        </div>
    </div>

    {{-- Tabs --}}
    <div class="border-b border-rule mb-8">
        <nav class="flex gap-8 -mb-[1px] overflow-x-auto pb-1 scrollbar-hide">
            <x-profile.tab-link :href="route('profile.show', $user->username)" :active="request()->routeIs('profile.show')">
                Profile
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.activity', $user->username)" :active="request()->routeIs('profile.activity')">
                Activity
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.reputation', $user->username)" :active="request()->routeIs('profile.reputation')">
                Reputation
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.following', $user->username)" :active="request()->routeIs('profile.following')">
                Following
            </x-profile.tab-link>

            <x-profile.tab-link :href="route('profile.followers', $user->username)" :active="request()->routeIs('profile.followers')">
                Followers
            </x-profile.tab-link>
        </nav>
    </div>

    {{-- Dynamic Content Area --}}
    <div class="mt-2">
        @yield('profile-content')
    </div>
</div>
@endsection