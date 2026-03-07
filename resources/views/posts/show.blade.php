@extends('layouts.app')

@section('content')
    <x-post.progress-bar />

    <div class="max-w-5xl mx-auto px-4 py-10 lg:flex lg:gap-16 animate-[fadeUp_0.8s_ease_both]">
        
        {{-- Main Article Area --}}
        <article class="lg:w-3/4 min-w-0">
            
            {{-- Breadcrumb Navigation --}}
            <nav class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-6 flex items-center gap-2">
                <a href="{{ route('posts.index') }}" class="hover:text-ink transition-colors border-b border-transparent hover:border-ink">Discussions</a>
                <span class="opacity-50">/</span>
                <span class="text-ink font-bold">Discussion</span>
            </nav>

            {{-- Discussion Title --}}
            <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold text-ink leading-[1.1] mb-6 tracking-tight">
                {{ $post->title }}
            </h1>

            {{-- Metadata Ribbon --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-serif text-muted italic border-b border-rule pb-6 mb-8">
                <div class="flex items-center gap-2">
                    <x-user-avatar :user="$post->user" size="xs" />
                    <span>Authored by <a href="{{ route('profile.show', $post->user->username) }}" class="font-bold text-ink hover:text-accent transition-colors">{{ $post->user->display_name }}</a></span>
                </div>
                <span class="opacity-30">&bull;</span>
                <span>{{ $post->created_at->format('M d, Y') }}</span>
                <span class="opacity-30">&bull;</span>
                <span class="flex items-center gap-1"><x-icons.eye class="w-4 h-4 opacity-60"/> {{ number_format($post->view_count) }} Views</span>

                {{-- Utility Actions --}}
                <div class="ml-auto flex items-center gap-3">
                    <x-post.share-button />
                    <x-action-dropdown>
                        <x-report-button type="post" :id="$post->id" />
                        
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
                <figure class="mb-12 w-full rounded-sm border border-rule shadow-sm bg-aged/5 flex justify-center items-center py-4 px-2">
                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="max-h-[500px] w-auto max-w-full object-contain grayscale hover:grayscale-0 transition-all duration-700">
                </figure>
            @endif

            <div class="flex gap-4 md:gap-8">
                
                {{-- Left Sidebar: Voting & Bookmarking --}}
                <div class="hidden sm:flex flex-col items-center shrink-0 w-8 pt-2">
                    <x-post-vote :post="$post" />
                    <div class="mt-6 border-t border-rule pt-4">
                        <x-post.save-post-button :post="$post" />
                    </div>
                </div>

                {{-- Right Body: Main Content --}}
                <div class="flex-1 min-w-0">
                    
                    {{-- Moderation Notice --}}
                    @if($post->is_hidden)
                        <div class="mb-8 p-5 bg-aged border-l-2 border-accent-warm text-ink text-sm font-serif italic shadow-sm">
                            <strong>Notice:</strong> This discussion is currently restricted from public view pending review for community guideline adherence.
                        </div>
                    @endif

                    {{-- Rendered Markdown Content --}}
                    <div class="prose prose-lg dark:prose-invert max-w-none text-ink font-serif leading-relaxed mb-12" id="post-content">
                        {!! \Illuminate\Support\Str::markdown($post->body, ['html_input' => 'escape']) !!}
                    </div>

                    {{-- Topics / Tags --}}
                    @if($post->tags->isNotEmpty())
                        <div class="flex flex-wrap items-center gap-2 pt-6 border-t border-rule mb-12">
                            <span class="font-mono text-[10px] uppercase tracking-widest text-muted mr-2">Topics:</span>
                            @foreach($post->tags as $tag)
                                <x-tag-badge :tag="$tag" />
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

            {{-- Discourse / Comments Section --}}
            <section class="mt-8 pt-10 border-t-4 border-double border-rule">
                <x-post.comment-section :post="$post" :comments="$comments" />
            </section>

        </article>

        {{-- Right Sidebar (Desktop) --}}
        <aside class="hidden lg:block lg:w-1/4 shrink-0">
            <x-post.sidebar-author :user="$post->user" />
        </aside>
    </div>
@endsection

@push('styles')
    <style>
        /* ==========================================================
           Academic Markdown Styling
           ========================================================== */
        
        /* Headers */
        #post-content h1, #post-content h2, #post-content h3, #post-content h4 {
            font-family: var(--font-heading);
            font-weight: 700;
            color: var(--color-ink);
            margin-top: 1.8em;
            margin-bottom: 0.8em;
            line-height: 1.2;
        }
        #post-content h1 { font-size: 2.25rem; border-bottom: 1px solid var(--color-rule); padding-bottom: 0.3em; }
        #post-content h2 { font-size: 1.875rem; }
        #post-content h3 { font-size: 1.5rem; }

        /* Blockquotes */
        #post-content blockquote {
            border-left: 4px double var(--color-rule);
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            font-style: italic;
            color: var(--color-muted);
            background-color: var(--color-aged);
            border-radius: 0.125rem;
        }

        /* Lists */
        #post-content ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1.2rem; }
        #post-content ol { list-style-type: decimal; padding-left: 1.5rem; margin-bottom: 1.2rem; }
        #post-content li { margin-bottom: 0.5rem; }

        /* Links & Bold */
        #post-content a { color: var(--color-accent); text-decoration: underline; text-underline-offset: 3px; transition: color 0.2s; }
        #post-content a:hover { color: var(--color-ink); }
        #post-content strong, #post-content b { font-weight: 700; color: var(--color-ink); }
        #post-content code { font-family: var(--font-mono); font-size: 0.85em; background: var(--color-aged); padding: 0.1em 0.3em; border-radius: 0.125rem; }

        /* Images */
        #post-content img {
            display: inline-block !important; 
            max-width: 48% !important; 
            max-height: 250px !important; 
            width: auto !important;
            height: auto !important;
            margin: 1rem 1% !important; 
            border-radius: 0.125rem;
            border: 1px solid var(--color-rule);
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            object-fit: contain;
            vertical-align: middle; 
            background-color: var(--color-paper);
        }
        
        @media (max-width: 640px) {
            #post-content img {
                max-width: 100% !important;
                max-height: 250px !important;
                display: block !important;
                margin: 1.5rem auto !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        /**
         * Initialize client-side rendering for Math expressions (KaTeX/MathJax)
         * and Syntax Highlighting (highlight.js) after the DOM loads.
         */
        document.addEventListener("DOMContentLoaded", function() {
            const contentContainer = document.getElementById('post-content');
            
            if (contentContainer && window.renderMathInElement && window.hljs) {
                // Render LaTeX equations
                window.renderMathInElement(contentContainer, {
                    delimiters: [
                        {left: "$$", right: "$$", display: true},
                        {left: "$", right: "$", display: false}
                    ],
                    throwOnError: false
                });

                // Highlight code blocks
                contentContainer.querySelectorAll('pre code').forEach((block) => {
                    window.hljs.highlightElement(block);
                });
            }
        });
    </script>
@endpush