@extends('admin.layouts.app')

@section('header', 'Manage Users')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 animate-[fadeUp_0.8s_ease_both]">

    @if(session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error">{{ session('error') }}</x-alert>
    @endif

    {{-- Search Toolbar --}}
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-paper p-4 border border-rule shadow-sm">
        <form method="GET" action="{{ route('admin.users.index') }}" class="w-full sm:w-96 relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-muted">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or username..." 
                   class="block w-full pl-10 pr-3 py-2 border border-rule bg-transparent text-ink font-serif text-sm focus:ring-0 focus:border-ink transition-colors">
        </form>
        
        <div class="font-mono text-[10px] uppercase tracking-widest text-muted font-bold">
            Total Users: {{ number_format($users->total()) }}
        </div>
    </div>

    {{-- Users Table --}}
    <div class="bg-paper border border-rule shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-rule">
                <thead class="bg-aged/30">
                    <tr>
                        <th class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">User Profile</th>
                        <th class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Role</th>
                        <th class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Reputation</th>
                        <th class="px-6 py-4 text-left font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Joined Date</th>
                        <th class="px-6 py-4 text-right font-mono text-[10px] font-bold text-muted uppercase tracking-widest">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-rule">
                    @forelse($users as $user)
                        <tr class="hover:bg-aged/10 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-4">
                                    <x-user-avatar :user="$user" size="md" class="{{ $user->is_banned ? 'grayscale opacity-50' : '' }}" />
                                    <div>
                                        <div class="text-sm font-bold text-ink font-serif flex items-center gap-2">
                                            <a href="{{ route('admin.users.show', $user) }}" class="hover:text-accent transition-colors">{{ $user->full_name }}</a>
                                            @if($user->is_banned)
                                                <span class="text-[9px] bg-accent-warm text-paper px-1.5 py-0.5 font-mono uppercase tracking-tighter">Banned</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-muted font-mono">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 font-mono text-[9px] font-bold uppercase tracking-widest border border-rule
                                    {{ $user->isAdmin() ? 'bg-ink text-paper border-ink' : ($user->isModerator() ? 'bg-aged text-ink' : 'text-muted') }}">
                                    {{ $user->role->name ?? 'User' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-sm text-ink font-bold">
                                {{ number_format($user->reputation_points) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-serif text-sm text-muted italic">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-muted hover:text-ink transition-colors" title="View Profile">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Permanently delete this user?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-muted hover:text-accent-warm transition-colors" title="Delete User">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center font-serif italic text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-rule bg-aged/10">{{ $users->links() }}</div>
        @endif
    </div>
</div>
@endsection