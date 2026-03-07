@extends('profile._layout')

@section('profile-content')
{{-- Alpine.js component for zero-latency client-side filtering --}}
<div class="max-w-4xl" x-data="{ 
    search: '',
    matches(text) {
        return text.toLowerCase().includes(this.search.toLowerCase())
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h3 class="font-heading text-2xl font-bold text-ink">Followers</h3>
            <p class="font-serif text-sm italic text-muted mt-1">Scholars following {{ $user->display_name }}</p>
        </div>

        @if(!$isPrivate)
        <div class="relative">
            <input type="text" 
                   x-model="search" 
                   placeholder="Search followers..." 
                   class="w-full md:w-64 pl-10 pr-4 py-2.5 bg-aged/10 border border-rule rounded-sm text-sm font-serif focus:ring-0 focus:border-ink transition-all text-ink placeholder:text-muted placeholder:italic">
            <div class="absolute left-3 top-3 text-muted">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
        @endif
    </div>

    @if($isPrivate)
        {{-- Elegant Soft-Gating UI for privacy --}}
        <div class="py-16 text-center border border-dashed border-rule bg-aged/10 rounded-sm">
            <div class="flex justify-center mb-4">
                <svg class="w-8 h-8 text-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <p class="font-serif text-lg text-muted italic">This list is classified as private.</p>
            <p class="font-mono text-[10px] uppercase tracking-widest text-muted mt-2">Only {{ $user->display_name }} holds access clearance.</p>
        </div>
    @else
        {{-- Reactive Grid Layout --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @forelse($followers as $follower)
                <div x-show="matches('{{ $follower->display_name }} {{ $follower->username }}')">
                    <x-user-card :user="$follower" />
                </div>
            @empty
                <div class="col-span-2 py-12 text-center text-muted font-serif italic border border-dashed border-rule bg-aged/5">
                    No followers recorded yet.
                </div>
            @endforelse
        </div>

        @if($followers->hasPages())
            <div class="mt-8 pt-4 border-t border-rule" x-show="search === ''">
                {{ $followers->links() }}
            </div>
        @endif
    @endif
</div>
@endsection