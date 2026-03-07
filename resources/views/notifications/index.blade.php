@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 animate-[fadeUp_0.8s_ease_both]">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-10 border-b border-rule pb-4">
        <div>
            <h1 class="font-heading text-4xl font-bold text-ink mb-2">Notifications</h1>
            <p class="font-serif text-lg text-muted italic">A record of your recent academic interactions.</p>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="font-mono text-[10px] uppercase tracking-[0.1em] px-4 py-1.5 border border-rule text-ink hover:bg-aged transition-colors rounded-sm shadow-sm focus:outline-none">
                    Mark all read
                </button>
            </form>

            <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Purge all records?');">
                @csrf @method('DELETE')
                <button class="font-mono text-[10px] uppercase tracking-[0.1em] px-4 py-1.5 border border-accent-warm/30 text-accent-warm hover:bg-accent-warm/10 transition-colors rounded-sm shadow-sm focus:outline-none">
                    Clear all
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="border border-rule bg-paper rounded-sm shadow-sm overflow-hidden">
        <div class="divide-y divide-rule">
            @forelse ($notifications as $notification)
                @include('notifications.partials.notification-item', ['notification' => $notification])
            @empty
                <div class="py-20 text-center bg-aged/10">
                    <div class="flex justify-center mb-4">
                        <svg class="w-8 h-8 text-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                    <h3 class="font-serif text-lg text-ink font-bold mb-1">No recent activity</h3>
                    <p class="font-serif text-sm text-muted italic">We will notify you when something occurs.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
</div>
@endsection