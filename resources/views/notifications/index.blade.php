@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 animate-[fadeUp_0.8s_ease_both]">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-6 mb-10 border-b border-rule pb-4">
        <div>
            <h1 class="font-heading text-4xl font-bold text-ink mb-2">Correspondence</h1>
            <p class="font-serif text-lg text-muted italic">A record of your recent academic interactions.</p>
        </div>

        <div class="flex items-center gap-4 shrink-0">
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="font-mono text-[10px] uppercase tracking-[0.1em] px-4 py-1.5 border border-rule text-ink hover:bg-aged transition-colors rounded-sm">
                    Mark all read
                </button>
            </form>

            <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Clear all?');">
                @csrf @method('DELETE')
                <button class="font-mono text-[10px] uppercase tracking-[0.1em] px-4 py-1.5 border border-[#a65a38]/30 text-[#a65a38] hover:bg-[#a65a38]/10 transition-colors rounded-sm">
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
                    <span class="block text-2xl mb-2 opacity-50">✦</span>
                    <h3 class="font-serif text-lg text-ink font-bold mb-1">No correspondence</h3>
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