@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 animate-[fadeUp_0.8s_ease_both]">
    
    {{-- Tag Header Component --}}
    <div class="mb-16 border-b-4 border-double border-rule pb-10"
         x-data="{ 
            isFollowing: {{ $isFollowing ? 'true' : 'false' }}, 
            count: {{ $tag->followers_count ?? 0 }},
            loading: false,
            toggleFollow() {
                if (this.loading) return;
                this.loading = true;
                const originalState = this.isFollowing;
                this.isFollowing = !this.isFollowing;
                this.count = this.isFollowing ? this.count + 1 : this.count - 1;

                fetch(this.isFollowing ? '{{ route('tags.follow', $tag) }}' : '{{ route('tags.unfollow', $tag) }}', {
                    method: this.isFollowing ? 'POST' : 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(response => { if (!response.ok) throw new Error(); })
                .catch(() => {
                    this.isFollowing = originalState;
                    this.count = this.isFollowing ? this.count + 1 : this.count - 1;
                    alert('Error updating follow status.');
                })
                .finally(() => this.loading = false);
            }
         }">
        
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6">
            
            <div class="min-w-0 flex-1">
                <div class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-4 flex items-center gap-2">
                    <a href="{{ route('tags.index') }}" class="hover:text-ink transition-colors border-b border-transparent hover:border-ink">Disciplines</a>
                    <span class="opacity-50">/</span>
                    <span class="text-ink font-bold">Archive</span>
                </div>

                <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink truncate mb-4">
                    {{ strtolower($tag->name) }}
                </h1>
                
                <p class="font-serif text-lg text-muted italic mb-6">
                    Browsing {{ number_format($posts->total()) }} entries tagged with this topic.
                </p>

                <div class="flex items-center gap-4 font-mono text-xs uppercase tracking-widest text-ink">
                    <span x-text="count">{{ $tag->followers_count ?? 0 }}</span>
                    <span x-text="count === 1 ? 'Scholar following' : 'Scholars following'"></span>
                </div>
            </div>
            
            @auth
                <div class="shrink-0 pt-2">
                    <button @click="toggleFollow()" 
                            :class="isFollowing 
                                ? 'text-muted border-rule hover:text-accent-warm hover:border-accent-warm' 
                                : 'text-paper bg-ink border-ink hover:bg-transparent hover:text-ink'"
                            class="font-mono text-xs uppercase tracking-[0.15em] px-6 py-2 border transition-all flex items-center justify-center gap-2"
                            :disabled="loading">
                        
                        <svg x-show="loading" class="animate-spin h-3 w-3 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" x-cloak>
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>

                        <span x-text="isFollowing ? 'Unfollow' : 'Follow Discipline'"></span>
                    </button>
                </div>
            @endauth
        </div>
    </div>

    {{-- Posts Feed --}}
    <div class="flex flex-col">
        @forelse($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="py-20 text-center border border-dashed border-rule bg-aged/10">
                <p class="font-serif text-muted italic text-lg mb-4">The archive is currently empty for this discipline.</p>
                <a href="{{ route('posts.create', ['tag' => $tag->name]) }}" class="font-mono text-xs uppercase tracking-[0.15em] text-ink border-b border-ink hover:text-accent hover:border-accent transition-colors">
                    Contribute an Entry &rarr;
                </a>
            </div>
        @endforelse

        <div class="mt-12">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection