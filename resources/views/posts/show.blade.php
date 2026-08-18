@extends('layouts.app')

@section('content')
    <x-post.progress-bar />

    <div class="max-w-5xl mx-auto px-4 py-8 lg:flex lg:gap-12 animate-[fadeUp_0.8s_ease_both]">
        
        {{-- Main Article Area --}}
        <article class="lg:w-3/4 min-w-0">
            
            {{-- Breadcrumb Navigation --}}
            <nav class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-6 flex items-center gap-2">
                <a href="{{ route('posts.index') }}" class="hover:text-ink transition-colors border-b border-transparent hover:border-ink">Discussions</a>
                <span class="opacity-50">/</span>
                <span class="text-ink font-bold">Discussion</span>
            </nav>

            {{-- Pinned Indicator --}}
            @if($post->isPinned())
                <div class="inline-flex items-center gap-1.5 mb-3 px-2.5 py-1 bg-accent/10 border border-accent/30 text-accent font-mono text-[10px] uppercase tracking-widest font-bold">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M16 9V4h1a1 1 0 0 0 0-2H7a1 1 0 0 0 0 2h1v5a3 3 0 0 1-3 3v2h5.97v7l1 1 1-1v-7H19v-2a3 3 0 0 1-3-3Z"/></svg>
                    Pinned Discussion
                </div>
            @endif

            {{-- Discussion Title (REDUCED SIZE FOR BETTER READABILITY) --}}
            <h1 class="font-heading text-3xl md:text-4xl lg:text-[2.5rem] font-bold text-ink leading-[1.2] mb-6 tracking-tight">
                {{ $post->title }}
            </h1>

            {{-- Metadata Ribbon --}}
            <div class="flex flex-wrap items-center justify-between gap-y-4 text-[13px] font-serif text-muted italic border-b border-rule pb-5 mb-8">
                
                {{-- Author & Date info --}}
                <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                    <div class="flex items-center gap-2">
                        <x-user-avatar :user="$post->user" size="xs" />
                        <span>Authored by <a href="{{ route('profile.show', $post->user->username) }}" class="not-italic font-semibold text-ink hover:text-accent transition-colors">{{ $post->user->display_name }}</a></span>
                    </div>
                    <span class="opacity-30 hidden sm:inline">&bull;</span>
                    <span>{{ $post->created_at->format('M d, Y') }}</span>
                    <span class="opacity-30 hidden sm:inline">&bull;</span>
                    <span class="flex items-center gap-1"><x-icons.eye class="w-4 h-4 opacity-60"/> {{ number_format($post->view_count) }} Views</span>
                </div>

                {{-- Utility Actions --}}
                <div class="flex items-center gap-2 shrink-0">
                    <x-post.share-button />
                    <x-action-dropdown>
                        <x-report-button type="post" :id="$post->id" />

                        {{-- Reputation-gated: pin / unpin own discussion (>= 250 reputation) --}}
                        @can('pin', $post)
                            <form action="{{ $post->isPinned() ? route('posts.unpin', $post) : route('posts.pin', $post) }}" method="POST">
                                @csrf
                                @if($post->isPinned()) @method('DELETE') @endif
                                <button type="submit" class="block w-full text-left px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition-colors">
                                    {{ $post->isPinned() ? 'Unpin from feed' : 'Pin to top of feed' }}
                                </button>
                            </form>
                        @endcan

                        @can('update', $post)
                            <a href="{{ route('posts.edit', $post) }}" class="block px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition-colors">Edit Discussion</a>
                        @endcan
                        
                        @can('delete', $post)
                            <div class="my-1 border-t border-rule"></div>
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Permanently delete this discussion?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="block w-full text-left px-4 py-1.5 text-sm font-serif text-accent-warm hover:bg-aged transition-colors">Delete Discussion</button>
                            </form>
                        @endcan
                    </x-action-dropdown>
                </div>
            </div>

            {{-- Cover Image --}}
            @if($post->image)
                <figure class="mb-10 w-full rounded-md border border-rule shadow-sm bg-aged/5 flex justify-center items-center py-4 px-2 overflow-hidden">
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="max-h-[400px] w-auto max-w-full object-contain hover:scale-[1.02] transition-transform duration-500">
                </figure>
            @endif

            <div class="flex gap-4 md:gap-8">
                
                {{-- Left Sidebar: Voting & Bookmarking --}}
                <div class="hidden sm:flex flex-col items-center shrink-0 w-10 pt-1">
                    <x-post-vote :post="$post" />
                    <div class="mt-5 border-t border-rule pt-4 w-full flex justify-center">
                        <x-post.save-post-button :post="$post" />
                    </div>
                </div>

                {{-- Right Body: Main Content --}}
                <div class="flex-1 min-w-0">
                    
                    {{-- Moderation Notice --}}
                    @if($post->is_hidden)
                        <div class="mb-6 p-4 bg-accent-warm/10 border-l-4 border-accent-warm text-ink text-sm font-serif shadow-sm flex items-start gap-3">
                            <svg class="w-5 h-5 text-accent-warm shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            <div>
                                <strong class="block mb-1">Moderation Notice</strong>
                                <span class="opacity-80">This discussion is restricted from public view due to community guideline violations.</span>
                            </div>
                        </div>
                    @endif

                    {{-- Rendered Markdown Content --}}
                    <div class="prose prose-lg dark:prose-invert max-w-none text-ink font-serif leading-[1.8] mb-10" id="post-content">
                        {!! $post->formatted_body !!}
                    </div>

                    {{-- Topics / Tags --}}
                    @if($post->tags->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-2 pt-6 border-t border-rule mb-10">
                            <span class="flex items-center gap-1 font-mono text-[10px] uppercase tracking-widest text-muted mr-2">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                                Topics:
                            </span>
                            @foreach($post->tags as $tag)
                                <x-tag-badge :tag="$tag" />
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

            {{-- Discourse / Comments Section --}}
            <section class="mt-4 pt-10 border-t border-rule relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-paper px-4 text-muted">
                    <svg class="w-5 h-5 opacity-50" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                </div>
                <x-post.comment-section :post="$post" :comments="$comments" />
            </section>

        </article>

        {{-- Right Sidebar (Desktop) --}}
        <aside class="hidden lg:block lg:w-1/4 shrink-0 border-l border-rule pl-8">
            <x-post.sidebar-author :user="$post->user" />
        </aside>
    </div>
@endsection

@push('styles')
    <style>
        /* ==========================================================
           Academic Markdown Styling (Refined for Readability)
           ========================================================== */
        
        /* Headers */
        #post-content h1, #post-content h2, #post-content h3, #post-content h4 {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--color-ink);
            margin-top: 1.8em;
            margin-bottom: 0.8em;
            line-height: 1.3;
        }
        #post-content h1 { font-size: 2rem; border-bottom: 1px solid var(--color-rule); padding-bottom: 0.3em; }
        #post-content h2 { font-size: 1.5rem; }
        #post-content h3 { font-size: 1.25rem; }

        /* Blockquotes */
        #post-content blockquote {
            border-left: 3px solid var(--color-ink);
            padding: 0.5rem 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: var(--color-muted);
            background-color: var(--color-aged);
            opacity: 0.9;
        }

        /* Lists */
        #post-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        #post-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.5rem; }
        #post-content li { margin-bottom: 0.5rem; padding-left: 0.25rem; }

        /* Links & Bold */
        #post-content a { color: var(--color-accent); text-decoration: none; border-bottom: 1px dotted var(--color-accent); transition: all 0.2s; }
        #post-content a:hover { color: var(--color-ink); border-bottom-style: solid; }
        #post-content strong, #post-content b { font-weight: 700; color: var(--color-ink); }
        #post-content code { font-family: var(--font-mono); font-size: 0.85em; background: var(--color-aged); padding: 0.2em 0.4em; border-radius: 0.25rem; border: 1px solid var(--color-rule); }

        /* Images */
        #post-content img {
            display: block !important; 
            max-width: 100% !important; 
            max-height: 400px !important; 
            margin: 2rem auto !important; 
            border-radius: 0.25rem;
            border: 1px solid var(--color-rule);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            object-fit: contain;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const contentContainer = document.querySelector('article');
            
            if (contentContainer && window.renderMathInElement && window.hljs) {
                window.renderMathInElement(contentContainer, {
                    delimiters: [
                        {left: "$$", right: "$$", display: true},
                        {left: "$", right: "$", display: false}
                    ],
                    throwOnError: false
                });

                contentContainer.querySelectorAll('pre code').forEach((block) => {
                    window.hljs.highlightElement(block);
                });
            }
        });
    </script>
@endpush