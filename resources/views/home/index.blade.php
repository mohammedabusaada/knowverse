@extends('layouts.app')

@section('content')
{{-- Applying a subtle SVG noise texture to the background for that "vintage paper" feel --}}
<div class="min-h-[80vh] bg-paper text-ink -mt-10 pb-20 relative" 
     style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.75\' numOctaves=\'4\' stitchTiles=\'stitch\'/%3E%3CfeColorMatrix type=\'saturate\' values=\'0\'/%3E%3C/filter%3E%3Crect width=\'400\' height=\'400\' filter=\'url(%23n)\' opacity=\'0.03\'/%3E%3C/svg%3E');">
    
    <div class="max-w-4xl mx-auto px-4 py-16 md:py-24">
        
        {{-- HERO SECTION --}}
        <section class="mb-20 animate-[fadeUp_0.8s_ease_both]">
            
            <h1 class="font-heading text-5xl md:text-7xl font-bold leading-tight tracking-tight mb-6">
                Where knowledge is<br>
                <em class="italic text-accent">rigorously</em> built.
            </h1>

            <p class="text-lg md:text-xl font-serif italic text-muted max-w-2xl leading-relaxed mb-10 pl-5 border-l-4 border-double border-rule">
                A platform for academic knowledge exchange — structured, peer-reviewed in spirit, and built for scholars who take discourse seriously.
            </p>

            <div class="flex flex-wrap items-center gap-6">
                @auth
                    <a href="{{ route('posts.create') }}" class="px-8 py-3.5 bg-ink text-paper text-sm font-bold tracking-wider hover:bg-transparent hover:text-ink border border-ink transition-colors shadow-sm">
                        Start a Discussion
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3.5 bg-ink text-paper text-sm font-bold tracking-wider hover:bg-transparent hover:text-ink border border-ink transition-colors shadow-sm">
                        Join the Community
                    </a>
                @endauth
                
                <a href="{{ route('posts.index') }}" class="font-mono text-[10px] tracking-[0.15em] uppercase text-muted border-b border-transparent pb-1 hover:text-ink hover:border-ink transition-colors">
                    Browse Discussions &rarr;
                </a>
            </div>
        </section>

        {{-- ORNAMENTAL DIVIDER --}}
        <div class="flex items-center gap-4 text-muted opacity-40 mb-16 select-none">
            <div class="flex-1 h-px bg-rule"></div>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15 10H22L16 15L18 22L12 17L6 22L8 15L2 10H9L12 2Z" opacity="0.3"/></svg>
            <div class="flex-1 h-px bg-rule"></div>
        </div>

        {{-- POPULAR TOPICS --}}
        <section class="mb-20 animate-[fadeUp_0.8s_0.2s_ease_both]">
            <div class="flex items-center gap-4 mb-6">
                <h2 class="font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold">
                    Trending Topics
                </h2>
                <div class="flex-1 h-px bg-rule"></div>
            </div>

            <div class="flex flex-wrap gap-3">
                @foreach ($popularTags as $tag)
                    <a href="{{ route('posts.index', ['tags[]' => $tag->name]) }}" 
                       class="px-4 py-2 font-mono text-[10px] tracking-widest uppercase text-ink bg-aged border border-rule rounded-sm hover:bg-ink hover:text-paper transition-colors shadow-sm">
                        <span class="opacity-40 font-serif mr-1">§</span> {{ strtolower($tag->name) }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- RECENT DISCUSSIONS --}}
        <section class="animate-[fadeUp_0.8s_0.35s_ease_both]">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4 flex-1">
                    <h2 class="font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold">
                        Recent Additions
                    </h2>
                    <div class="hidden sm:block flex-1 h-px bg-rule mx-4"></div>
                </div>
                <a href="{{ route('posts.index') }}" class="font-mono text-[10px] tracking-[0.15em] uppercase text-muted hover:text-ink transition-colors">
                    View All &rarr;
                </a>
            </div>

            <div class="flex flex-col border-t border-rule">
                @forelse ($recentPosts as $index => $post)
                    <a href="{{ route('posts.show', $post) }}" class="group grid grid-cols-[2.5rem_1fr] sm:grid-cols-[2.5rem_1fr_auto] gap-4 sm:gap-6 items-start py-5 border-b border-rule hover:bg-aged/20 transition-colors">
                        
                        {{-- Row Index (01, 02...) --}}
                        <span class="font-mono text-[10px] text-muted opacity-50 pt-1.5 text-right">
                            {{ sprintf('%02d', $index + 1) }}
                        </span>

                        {{-- Content --}}
                        <div>
                            <h3 class="font-heading text-lg font-bold leading-snug mb-1.5 group-hover:text-accent transition-colors">
                                {{ $post->title }}
                            </h3>
                            <div class="flex flex-wrap gap-3 text-[13px] text-muted font-serif italic">
                                <span class="font-bold text-ink">{{ $post->user->display_name }}</span>
                                <span class="opacity-50">&bull;</span>
                                <span>{{ $post->comments_count ?? 0 }} responses</span>
                                <span class="opacity-50">&bull;</span>
                                <span>{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Primary Tag Badge --}}
                        @if($post->tags->count() > 0)
                            <div class="hidden sm:flex justify-end pt-1">
                                <span class="font-mono text-[9px] tracking-widest uppercase text-muted border border-rule px-2 py-0.5 rounded-sm bg-paper">
                                    {{ strtolower($post->tags->first()->name) }}
                                </span>
                            </div>
                        @endif
                    </a>
                @empty
                    <div class="py-16 text-center text-muted italic font-serif border border-dashed border-rule mt-4 bg-aged/10">
                        There are no discussions currently available.
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection