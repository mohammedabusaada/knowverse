@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#f7f4ef] dark:bg-[#141210] text-[#1a1714] dark:text-[#ede8df] -mt-10" 
     style="font-family: 'EB Garamond', Georgia, serif; background-image: url('data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'400\'%3E%3Cfilter id=\'n\'%3E%3CfeTurbulence type=\'fractalNoise\' baseFrequency=\'0.75\' numOctaves=\'4\' stitchTiles=\'stitch\'/%3E%3CfeColorMatrix type=\'saturate\' values=\'0\'/%3E%3C/filter%3E%3Crect width=\'400\' height=\'400\' filter=\'url(%23n)\' opacity=\'0.04\'/%3E%3C/svg%3E');">
    
    <div class="max-w-4xl mx-auto px-6 py-16 md:py-24">
        
        {{-- HERO SECTION --}}
        <section class="mb-20 animate-[fadeUp_0.8s_ease_both]">
            
            <h1 class="text-5xl md:text-7xl font-bold leading-tight tracking-tight mb-6" style="font-family: 'Libre Baskerville', Georgia, serif;">
                Where knowledge is<br>
                <em class="italic text-[#2b4a7a] dark:text-[#6a8ac0]">rigorously</em> built.
            </h1>

            <p class="text-lg md:text-xl italic text-[#8c8070] dark:text-[#7a7060] max-w-2xl leading-relaxed mb-10 pl-5 border-l-4 border-double border-[#1a1714]/15 dark:border-[#ede8df]/15">
                A platform for academic knowledge exchange — structured, peer-reviewed in spirit, and built for scholars who take discourse seriously.
            </p>

            <div class="flex flex-wrap items-center gap-6">
                @auth
                    <a href="{{ route('posts.create') }}" class="px-8 py-3.5 bg-[#1a1714] dark:bg-[#ede8df] text-[#f7f4ef] dark:text-[#141210] text-sm font-bold tracking-wider hover:bg-[#2b4a7a] dark:hover:bg-[#6a8ac0] transition-colors" style="font-family: 'Libre Baskerville', serif;">
                        Start a Discussion
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-3.5 bg-[#1a1714] dark:bg-[#ede8df] text-[#f7f4ef] dark:text-[#141210] text-sm font-bold tracking-wider hover:bg-[#2b4a7a] dark:hover:bg-[#6a8ac0] transition-colors" style="font-family: 'Libre Baskerville', serif;">
                        Join the Community
                    </a>
                @endauth
                <a href="{{ route('posts.index') }}" class="text-xs tracking-[0.15em] uppercase text-[#8c8070] dark:text-[#7a7060] border-b border-[#1a1714]/15 dark:border-[#ede8df]/15 pb-1 hover:text-[#2b4a7a] dark:hover:text-[#6a8ac0] hover:border-[#2b4a7a] transition-colors" style="font-family: 'DM Mono', monospace;">
                    Browse discussions &rarr;
                </a>
            </div>
        </section>

        {{-- ORNAMENTAL RULE --}}
        <div class="flex items-center gap-4 text-[#8c8070] opacity-40 mb-16">
            <div class="flex-1 h-px bg-[#1a1714]/20 dark:bg-[#ede8df]/20"></div>
            <span class="text-lg tracking-[0.3em]">✦ &nbsp; ✦ &nbsp; ✦</span>
            <div class="flex-1 h-px bg-[#1a1714]/20 dark:bg-[#ede8df]/20"></div>
        </div>

        {{-- POPULAR TOPICS --}}
        <section class="mb-20 animate-[fadeUp_0.8s_0.2s_ease_both]">
            <div class="flex items-center gap-4 mb-6">
                <h2 class="text-xs tracking-[0.2em] uppercase text-[#8c8070] dark:text-[#7a7060]" style="font-family: 'DM Mono', monospace;">
                    Popular Topics
                </h2>
                <div class="flex-1 h-px bg-[#1a1714]/15 dark:bg-[#ede8df]/15"></div>
            </div>

            <div class="flex flex-wrap gap-3">
                @foreach ($popularTags as $tag)
                    <a href="{{ route('posts.index', ['tags[]' => $tag->name]) }}" 
                       class="px-4 py-1.5 text-xs tracking-wider text-[#1a1714] dark:text-[#ede8df] bg-[#ede8df] dark:bg-[#1f1c18] border border-[#1a1714]/10 dark:border-[#ede8df]/10 rounded-sm hover:bg-[#1a1714] hover:text-[#f7f4ef] dark:hover:bg-[#ede8df] dark:hover:text-[#141210] transition-colors"
                       style="font-family: 'DM Mono', monospace;">
                        <span class="opacity-40">§</span> {{ strtolower($tag->name) }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- RECENT DISCUSSIONS --}}
        <section class="animate-[fadeUp_0.8s_0.35s_ease_both]">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4 flex-1">
                    <h2 class="text-xs tracking-[0.2em] uppercase text-[#8c8070] dark:text-[#7a7060]" style="font-family: 'DM Mono', monospace;">
                        Recent Discussions
                    </h2>
                    <div class="hidden sm:block flex-1 h-px bg-[#1a1714]/15 dark:bg-[#ede8df]/15 mx-4"></div>
                </div>
                <a href="{{ route('posts.index') }}" class="text-[10px] tracking-[0.15em] uppercase text-[#8c8070] dark:text-[#7a7060] hover:text-[#2b4a7a] transition-colors" style="font-family: 'DM Mono', monospace;">
                    Browse all &rarr;
                </a>
            </div>

            <div class="flex flex-col border-t border-[#1a1714]/15 dark:border-[#ede8df]/15">
                @forelse ($recentPosts as $index => $post)
                    <a href="{{ route('posts.show', $post) }}" class="group grid grid-cols-[2.5rem_1fr] sm:grid-cols-[2.5rem_1fr_auto] gap-4 sm:gap-6 items-start py-5 border-b border-[#1a1714]/15 dark:border-[#ede8df]/15 hover:bg-[#2b4a7a]/[0.03] dark:hover:bg-[#ede8df]/5 transition-colors">
                        
                        {{-- Number (01, 02...) --}}
                        <span class="text-xs text-[#8c8070] dark:text-[#7a7060] opacity-50 pt-1 text-right" style="font-family: 'DM Mono', monospace;">
                            {{ sprintf('%02d', $index + 1) }}
                        </span>

                        {{-- Content --}}
                        <div>
                            <h3 class="text-lg font-bold leading-snug mb-1.5 group-hover:text-[#2b4a7a] dark:group-hover:text-[#6a8ac0] transition-colors" style="font-family: 'Libre Baskerville', serif;">
                                {{ $post->title }}
                            </h3>
                            <div class="flex flex-wrap gap-3 text-sm text-[#8c8070] dark:text-[#7a7060] italic">
                                <span>{{ $post->user->display_name }}</span>
                                <span>{{ $post->comments_count }} replies</span>
                                <span>{{ $post->created_at->diffForHumans() }}</span>
                            </div>
                        </div>

                        {{-- Primary Tag --}}
                        @if($post->tags->count() > 0)
                            <span class="hidden sm:inline-block mt-1 text-[10px] tracking-wider uppercase text-[#2b4a7a] dark:text-[#6a8ac0] bg-[#2b4a7a]/5 dark:bg-[#6a8ac0]/10 px-2 py-1 rounded-sm" style="font-family: 'DM Mono', monospace;">
                                {{ strtolower($post->tags->first()->name) }}
                            </span>
                        @endif
                    </a>
                @empty
                    <div class="py-12 text-center text-[#8c8070] italic border border-dashed border-[#1a1714]/15 dark:border-[#ede8df]/15 mt-4">
                        The archive is currently empty.
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>

<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection