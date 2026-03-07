@extends('admin.layouts.app')

@section('header', 'User Profile Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 animate-[fadeUp_0.8s_ease_both]">
    
    <div>
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors border-b border-transparent hover:border-ink pb-1">
            &larr; Back to Users
        </a>
    </div>

    <div class="bg-paper border border-rule p-8 sm:p-12 space-y-10 shadow-sm relative overflow-hidden">
        
        {{-- Status Overlay --}}
        @if($user->is_banned)
            <div class="absolute top-0 right-0 bg-accent-warm text-paper px-10 py-1 font-mono text-[10px] uppercase tracking-widest rotate-45 translate-x-8 translate-y-4 shadow-sm font-bold">
                Banned
            </div>
        @endif

        {{-- Profile Summary --}}
        <div class="flex flex-col sm:flex-row items-center gap-8 border-b border-rule pb-10">
            <x-user-avatar :user="$user" size="lg" class="w-32 h-32 {{ $user->is_banned ? 'grayscale opacity-50' : '' }}" />
            <div class="text-center sm:text-left flex-1">
                <h1 class="font-heading text-4xl font-bold text-ink mb-1">{{ $user->full_name }}</h1>
                <p class="font-mono text-sm text-muted mb-4">{{ '@' . $user->username }}</p>
                <div class="flex flex-wrap justify-center sm:justify-start gap-4">
                    <span class="px-3 py-1 bg-aged text-ink border border-rule font-mono text-[10px] uppercase tracking-widest font-bold">
                        Role: {{ $user->role->name }}
                    </span>
                    <span class="px-3 py-1 bg-aged text-ink border border-rule font-mono text-[10px] uppercase tracking-widest font-bold">
                        Reputation: {{ number_format($user->reputation_points) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Information Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div>
                <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2 font-bold">User Information</h3>
                <dl class="space-y-4 text-sm font-serif">
                    <div>
                        <dt class="text-muted italic inline">Email:</dt>
                        <dd class="text-ink font-bold inline ml-2">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted italic inline">Joined:</dt>
                        <dd class="text-ink font-bold inline ml-2">{{ $user->created_at->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-muted italic inline">Verified:</dt>
                        <dd class="text-ink font-bold inline ml-2">{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y') : 'Pending' }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="font-mono text-[10px] text-muted uppercase tracking-[0.2em] mb-4 border-b border-rule pb-2 font-bold">Activity Stats</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-aged/30 border border-rule text-center">
                        <span class="block text-xl font-heading font-bold text-ink">{{ $user->posts_count ?? $user->posts()->count() }}</span>
                        <span class="text-[9px] font-mono text-muted uppercase tracking-widest">Posts</span>
                    </div>
                    <div class="p-4 bg-aged/30 border border-rule text-center">
                        <span class="block text-xl font-heading font-bold text-ink">{{ $user->all_comments_count ?? $user->allComments()->count() }}</span>
                        <span class="text-[9px] font-mono text-muted uppercase tracking-widest">Comments</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Danger Zone Actions --}}
        <div class="pt-10 border-t border-accent-warm/30 flex flex-col sm:flex-row gap-4 p-6 bg-accent-warm/5 mt-8">
            
            {{-- Form Dedicated to Banning/Unbanning --}}
            <form method="POST" action="{{ route('admin.users.toggle-ban', $user) }}" class="flex-1">
                @csrf @method('PATCH')
                @if(!$user->is_banned)
                    <button type="submit" class="w-full py-3 border border-accent-warm text-accent-warm font-mono text-[10px] uppercase tracking-widest hover:bg-accent-warm hover:text-paper transition-all focus:outline-none font-bold">
                        Ban User
                    </button>
                @else
                     <button type="submit" class="w-full py-3 border border-ink text-ink font-mono text-[10px] uppercase tracking-widest hover:bg-ink hover:text-paper transition-all focus:outline-none font-bold">
                        Unban User
                    </button>
                @endif
            </form>

            @if(auth()->id() !== $user->id)
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="flex-1" onsubmit="return confirm('DANGER: This action is permanent and will delete all user data. Proceed?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full py-3 bg-accent-warm text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm focus:outline-none font-bold">
                        Delete User
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection