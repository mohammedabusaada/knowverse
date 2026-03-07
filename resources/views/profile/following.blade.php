@extends('profile._layout')

@section('profile-content')
{{-- Multi-tab Alpine.js state manager with integrated search --}}
<div class="max-w-4xl" x-data="{ 
    tab: 'people', 
    search: '',
    matches(text) {
        return text.toLowerCase().includes(this.search.toLowerCase())
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h3 class="font-heading text-2xl font-bold text-ink">Following</h3>
            <p class="font-serif text-sm italic text-muted mt-1">Monitored scholars and research topics</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            {{-- Instant Search Field --}}
            <div class="relative w-full sm:w-auto">
                <input type="text" 
                       x-model="search" 
                       placeholder="Filter records..." 
                       class="w-full md:w-64 pl-10 pr-4 py-2 bg-aged/10 border border-rule rounded-sm text-sm font-serif focus:ring-0 focus:border-ink transition-all text-ink placeholder:text-muted placeholder:italic">
                <div class="absolute left-3 top-2.5 text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Context Switcher (Users vs Tags) --}}
            <div class="flex bg-aged/30 p-1 rounded-sm shrink-0 border border-rule">
                <button @click="tab = 'people'; search = ''" 
                    :class="tab === 'people' ? 'bg-ink shadow-sm text-paper' : 'text-muted hover:text-ink'"
                    class="px-5 py-1.5 font-mono text-[10px] uppercase tracking-[0.15em] rounded-sm transition-all focus:outline-none">
                    Scholars
                </button>
                <button @click="tab = 'tags'; search = ''" 
                    :class="tab === 'tags' ? 'bg-ink shadow-sm text-paper' : 'text-muted hover:text-ink'"
                    class="px-5 py-1.5 font-mono text-[10px] uppercase tracking-[0.15em] rounded-sm transition-all focus:outline-none">
                    Topics
                </button>
            </div>
        </div>
    </div>

    @if($isPrivate)
        {{-- Elegant Soft-Gating UI for privacy --}}
        <div class="py-16 text-center border border-dashed border-rule bg-aged/10 rounded-sm">
            <div class="flex justify-center mb-4">
                <svg class="w-8 h-8 text-muted opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <p class="font-serif text-lg text-muted italic">This following list is classified as private.</p>
        </div>
    @else
        {{-- Tab 1: Scholar Following --}}
        <div x-show="tab === 'people'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($following as $followedUser)
                    <div x-show="matches('{{ $followedUser->display_name }} {{ $followedUser->username }}')">
                        <x-user-card :user="$followedUser" />
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center text-muted font-serif italic border border-dashed border-rule bg-aged/5">Not following any scholars yet.</div>
                @endforelse
            </div>
            <div class="mt-8 pt-4 border-t border-rule" x-show="search === ''">{{ $following->appends(['tab' => 'people'])->links() }}</div>
        </div>

        {{-- Tab 2: Topic Following --}}
        <div x-show="tab === 'tags'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($followingTags as $tag)
                    <div x-show="matches('{{ $tag->name }}')" 
                         class="bg-paper p-5 rounded-sm border border-rule flex items-center justify-between group hover:border-ink transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 flex-none bg-aged border border-rule rounded-sm flex items-center justify-center text-muted group-hover:text-ink transition-colors">
                                <span class="font-serif text-lg opacity-40">§</span>
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tags.show', $tag->slug) }}" class="font-heading font-bold text-ink hover:text-accent-warm block truncate text-lg transition-colors">
                                    {{ strtolower($tag->name) }}
                                </a>
                                <p class="font-mono text-[9px] text-muted uppercase tracking-widest mt-1">{{ number_format($tag->posts_count ?? 0) }} documented records</p>
                            </div>
                        </div>

                        {{-- Asynchronous Unfollow Action --}}
                        @if(auth()->id() === $user->id)
                            <button x-data="{ unfollowed: false }" 
                                    x-show="!unfollowed"
                                    @click="fetch('{{ route('tags.unfollow', $tag) }}', {method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' }}); unfollowed = true"
                                    class="font-mono text-[9px] uppercase tracking-widest text-muted hover:text-accent-warm transition-colors ml-2 shrink-0 focus:outline-none">
                                Unfollow
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center text-muted font-serif italic border border-dashed border-rule bg-aged/5">Not following any topics yet.</div>
                @endforelse
            </div>
            <div class="mt-8 pt-4 border-t border-rule" x-show="search === ''">{{ $followingTags->appends(['tab' => 'tags'])->links() }}</div>
        </div>
    @endif
</div>
@endsection