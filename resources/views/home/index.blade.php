@extends('layouts.app')

@section('content')
{{-- Applying a subtle SVG noise texture to the background for that "vintage paper" feel --}}
<div class="min-h-[80vh] bg-paper text-ink -mt-10 pb-20 relative"
     style="background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.75\' numOctaves=\'4\' stitchTiles=\'stitch\'/%3E%3CfeColorMatrix type=\'saturate\' values=\'0\'/%3E%3C/filter%3E%3Crect width=\'400\' height=\'400\' filter=\'url(%23n)\' opacity=\'0.03\'/%3E%3C/svg%3E');">

    <div class="max-w-6xl mx-auto px-4 py-16 md:py-24">

        {{-- HERO SECTION --}}
        <section class="mb-14 animate-[fadeUp_0.8s_ease_both]">
            <div class="grid md:grid-cols-[1fr_1.2fr] gap-8 items-center">

                {{-- Left Side: The Main Heading --}}
                <div>
                    <h1 class="font-heading text-4xl md:text-5xl font-bold leading-tight tracking-tight">
                        Where knowledge is<br>
                        <em class="italic text-accent">rigorously</em> built.
                    </h1>
                </div>

                {{-- Right Side: Description and Buttons --}}
                <div class="pl-0 md:pl-8 md:border-l border-rule">
                    <p class="text-[15px] font-serif text-muted leading-relaxed mb-6">
                        A platform for academic knowledge exchange. Structured, community-validated, and built for scholars who take discourse seriously.
                    </p>
                    <div class="flex flex-wrap items-center gap-4">
                        @auth
                            <a href="{{ route('posts.create') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink text-paper text-xs font-bold tracking-wider hover:bg-transparent hover:text-ink border border-ink transition-all duration-300 hover:-translate-y-0.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                                Start a Discussion
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-ink text-paper text-xs font-bold tracking-wider hover:bg-transparent hover:text-ink border border-ink transition-all duration-300 hover:-translate-y-0.5 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 19.5l-3-3.5m0 0l-3 3.5m3-3.5v7.5M12 4.5h.01M6 4.5h.01M9 4.5h.01M2.25 12h19.5" /></svg>
                                Join the Community
                            </a>
                        @endauth
                    </div>
                </div>

            </div>
        </section>

        {{-- ORNAMENTAL DIVIDER --}}
        <div class="flex items-center gap-4 text-muted opacity-40 mb-12 select-none">
            <div class="flex-1 h-px bg-rule"></div>
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L15 10H22L16 15L18 22L12 17L6 22L8 15L2 10H9L12 2Z" opacity="0.3"/></svg>
            <div class="flex-1 h-px bg-rule"></div>
        </div>

        {{-- TWO-COLUMN LAYOUT: Main feed + Right rail --}}
        <div class="grid lg:grid-cols-[1fr_300px] gap-10 lg:gap-14 items-start">

            {{-- =========================================================
                 MAIN COLUMN — Recent Discussions
                 ========================================================= --}}
            <div class="min-w-0 animate-[fadeUp_0.8s_0.2s_ease_both]">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3 flex-1">
                        <h2 class="flex items-center gap-2 font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                            Recent Discussions
                        </h2>
                        <div class="hidden sm:block flex-1 h-px bg-rule mx-4"></div>
                    </div>
                    <a href="{{ route('posts.index') }}" class="group inline-flex items-center gap-1 font-mono text-[10px] tracking-[0.15em] uppercase text-muted hover:text-ink transition-colors shrink-0">
                        View All
                        <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" /></svg>
                    </a>
                </div>

                <div class="flex flex-col border-t border-rule">
                    @forelse ($recentPosts as $index => $post)
                        <a href="{{ route('posts.show', $post) }}" class="group grid grid-cols-[2.5rem_1fr] sm:grid-cols-[2.5rem_1fr_auto] gap-4 sm:gap-6 items-start py-5 px-3 border-b border-rule hover:bg-aged/10 hover:shadow-sm rounded-lg sm:rounded-none transition-all duration-300">

                            {{-- Row Index (01, 02...) --}}
                            <span class="font-mono text-[10px] text-muted opacity-50 pt-1.5 text-right group-hover:text-accent transition-colors">
                                {{ sprintf('%02d', $index + 1) }}
                            </span>

                            {{-- Content --}}
                            <div class="min-w-0">
                                <h3 class="font-heading text-lg font-bold leading-snug mb-2 group-hover:text-accent transition-colors truncate sm:whitespace-normal">
                                    {{ $post->title }}
                                </h3>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[12px] text-muted font-serif italic">
                                    <span class="flex items-center gap-1 not-italic font-semibold text-ink">
                                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                                        {{ $post->user->display_name }}
                                    </span>
                                    <span class="opacity-50 hidden sm:inline">&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" /></svg>
                                        {{ $post->comments_count ?? 0 }}
                                    </span>
                                    <span class="opacity-50 hidden sm:inline">&bull;</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            {{-- Primary Tag Badge & Chevron --}}
                            <div class="hidden sm:flex flex-col items-end gap-2 pt-1">
                                @if($post->tags->count() > 0)
                                    <span class="font-mono text-[9px] tracking-widest uppercase text-muted border border-rule px-2 py-1 rounded-md bg-paper group-hover:border-accent/30 group-hover:text-accent transition-colors shadow-sm">
                                        #{{ strtolower($post->tags->first()->name) }}
                                    </span>
                                @endif
                                <svg class="w-4 h-4 text-muted opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" /></svg>
                            </div>
                        </a>
                    @empty
                        {{-- Empty State with Icon --}}
                        <div class="py-20 flex flex-col items-center justify-center text-center border border-dashed border-rule mt-4 bg-aged/5 rounded-xl">
                            <svg class="w-12 h-12 mb-4 text-muted opacity-20" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                            <p class="text-muted italic font-serif text-lg">There are no discussions currently available.</p>
                            <p class="text-sm font-mono text-muted/60 mt-2">Be the first to start a conversation.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- =========================================================
                 RIGHT RAIL — Standing, Top Scholars, Topics, Stats
                 ========================================================= --}}
            <aside class="space-y-10 lg:sticky lg:top-24 animate-[fadeUp_0.8s_0.35s_ease_both]">

                @auth
                {{-- Your Academic Standing --}}
                <div>
                    <h3 class="flex items-center gap-2 font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold mb-4">
                        Your Standing
                        <span class="flex-1 h-px bg-rule"></span>
                    </h3>
                    <a href="{{ route('profile.reputation', auth()->user()->username) }}" class="block border border-rule bg-aged/20 p-5 shadow-sm hover:border-accent/40 transition-colors">
                        <div class="font-heading text-2xl font-bold {{ auth()->user()->getStandingColor() }} leading-tight">
                            {{ auth()->user()->getAcademicStanding() }}
                        </div>
                        <div class="font-mono text-[10px] uppercase tracking-widest text-muted mt-2">
                            {{ number_format(auth()->user()->reputation_points) }} Reputation
                        </div>
                    </a>
                </div>
                @endauth

                {{-- Top Scholars --}}
                <div>
                    <h3 class="flex items-center gap-2 font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold mb-4">
                        Top Scholars
                        <span class="flex-1 h-px bg-rule"></span>
                    </h3>
                    <div class="space-y-4">
                        @forelse ($topScholars as $i => $scholar)
                            <a href="{{ route('profile.show', $scholar->username) }}" class="flex items-center gap-3 group">
                                <span class="font-mono text-[11px] text-muted opacity-60 w-3 text-right shrink-0">{{ $i + 1 }}</span>
                                <x-user-avatar :user="$scholar" size="xs" />
                                <div class="min-w-0 flex-1">
                                    <div class="font-semibold text-ink text-[13px] truncate group-hover:text-accent transition-colors">{{ $scholar->display_name }}</div>
                                    <div class="font-mono text-[9px] uppercase tracking-widest text-muted">{{ number_format($scholar->reputation_points) }} pts</div>
                                </div>
                            </a>
                        @empty
                            <p class="text-muted italic font-serif text-sm">No scholars ranked yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Trending Topics --}}
                <div>
                    <h3 class="flex items-center gap-2 font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold mb-4">
                        Trending Topics
                        <span class="flex-1 h-px bg-rule"></span>
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        @forelse ($popularTags->take(8) as $tag)
                            <a href="{{ route('posts.index', ['tags[]' => $tag->name]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 font-mono text-[9px] tracking-widest uppercase text-ink bg-aged border border-rule rounded-sm hover:bg-ink hover:text-paper transition-all duration-300">
                                <x-tag-icon :slug="$tag->slug" classes="w-3 h-3 opacity-60" />
                                {{ strtolower($tag->name) }}
                            </a>
                        @empty
                            <p class="text-muted italic font-serif text-sm">No topics yet.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Community Stats --}}
                <div>
                    <h3 class="flex items-center gap-2 font-mono text-[10px] tracking-[0.2em] uppercase text-muted font-bold mb-4">
                        Community
                        <span class="flex-1 h-px bg-rule"></span>
                    </h3>
                    <div class="grid grid-cols-3 border border-rule divide-x divide-rule bg-aged/10">
                        <div class="p-3 text-center">
                            <span class="block font-heading text-xl font-bold text-ink">{{ number_format($stats['discussions']) }}</span>
                            <span class="font-mono text-[8px] uppercase tracking-widest text-muted">Posts</span>
                        </div>
                        <div class="p-3 text-center">
                            <span class="block font-heading text-xl font-bold text-ink">{{ number_format($stats['scholars']) }}</span>
                            <span class="font-mono text-[8px] uppercase tracking-widest text-muted">Scholars</span>
                        </div>
                        <div class="p-3 text-center">
                            <span class="block font-heading text-xl font-bold text-ink">{{ number_format($stats['comments']) }}</span>
                            <span class="font-mono text-[8px] uppercase tracking-widest text-muted">Comments</span>
                        </div>
                    </div>
                </div>

            </aside>
        </div>
    </div>
</div>
@endsection
