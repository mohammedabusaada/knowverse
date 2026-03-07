@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 animate-[fadeUp_0.8s_ease_both]" x-data="{ search: '' }">
    
    {{-- Header & Search --}}
    <div class="mb-12">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink tracking-tight mb-4">Explore Tags</h1>
        <p class="font-serif text-lg text-muted italic mb-8">
            Follow your favorite academic topics.
        </p>

        {{-- Search Filter --}}
        <div class="relative w-full max-w-lg border-b border-rule pb-2">
            <div class="absolute inset-y-0 left-0 pl-1 flex items-center pointer-events-none pb-2">
                <span class="font-serif text-lg text-muted opacity-50">§</span>
            </div>
            <input 
                x-model="search"
                type="text" 
                placeholder="Filter tags..." 
                class="block w-full pl-6 pr-3 py-2 bg-transparent border-none text-ink font-serif text-lg placeholder:text-muted focus:ring-0 transition-colors"
            >
        </div>
    </div>

    {{-- Tags List (Replaced Grid with Classic List) --}}
    <div class="flex flex-col border-t border-rule">
        @foreach($tags as $tag)
            @php
                $isFollowing = auth()->check() && auth()->user()->followedTags->contains($tag->id);
            @endphp

            <div 
                x-show="'{{ strtolower($tag->name) }}'.includes(search.toLowerCase())"
                x-data="{ 
                    following: {{ $isFollowing ? 'true' : 'false' }},
                    toggle() {
                        this.following = !this.following;
                        fetch('{{ route('tags.follow', $tag) }}', {
                            method: this.following ? 'POST' : 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).catch(() => this.following = !this.following);
                    }
                }"
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 py-6 border-b border-rule hover:bg-aged/20 transition-colors group px-2"
            >
                <div class="min-w-0 flex-1">
                    <h2 class="font-heading text-xl font-bold text-ink mb-1">
                        <a href="{{ route('tags.show', $tag->slug) }}" class="hover:text-accent transition-colors">
                            {{ strtolower($tag->name) }}
                        </a>
                    </h2>
                    
                    <p class="font-serif text-[15px] text-muted line-clamp-2 leading-relaxed italic mb-2">
                        {{ $tag->description ?? 'Academic discussions and research related to ' . strtolower($tag->name) . '.' }}
                    </p>

                    <div class="flex items-center gap-4 font-mono text-[10px] uppercase tracking-widest text-muted">
                        <span>{{ number_format($tag->posts_count) }} discussions</span>
                        <span>&bull;</span>
                        <a href="{{ route('tags.show', $tag->slug) }}" class="text-ink hover:text-accent transition-colors">
                            Explore &rarr;
                        </a>
                    </div>
                </div>

                <div class="shrink-0 sm:self-start mt-2 sm:mt-0">
                    @auth
                        <button 
                            @click="toggle()"
                            :class="following ? 'text-muted border-rule hover:text-accent-warm hover:border-accent-warm' : 'text-ink border-ink hover:bg-ink hover:text-paper'"
                            class="font-mono text-[10px] uppercase tracking-[0.15em] px-4 py-1.5 border transition-all"
                        >
                            <span x-text="following ? 'Unfollow' : 'Follow'"></span>
                        </button>
                    @endauth
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-12">
        {{ $tags->links() }}
    </div>
</div>

<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection