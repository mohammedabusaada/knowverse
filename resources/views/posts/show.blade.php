@extends('layouts.app')

@section('content')
    <x-post.progress-bar />

    <div class="max-w-5xl mx-auto px-4 py-10 lg:flex lg:gap-16 animate-[fadeUp_0.8s_ease_both]">
        
        {{-- Main Article Area --}}
        <article class="lg:w-3/4 min-w-0">
            
            {{-- Breadcrumb --}}
            <nav class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted mb-8 flex items-center gap-2">
                <a href="{{ route('posts.index') }}" class="hover:text-ink transition-colors border-b border-transparent hover:border-ink">The Archive</a>
                <span class="opacity-50">/</span>
                <span class="text-ink font-bold">Discussion</span>
            </nav>

            {{-- Title --}}
            <h1 class="font-heading text-4xl md:text-5xl lg:text-6xl font-bold text-ink leading-[1.1] mb-8 tracking-tight">
                {{ $post->title }}
            </h1>

            <div class="flex gap-4 md:gap-8">
                
                {{-- Left Side: Voting (Vertical) --}}
                <div class="hidden sm:flex flex-col items-center shrink-0 w-8 pt-2">
                    <x-post-vote :post="$post" />
                    <div class="mt-6 border-t border-rule pt-4">
                        <x-post.save-post-button :post="$post" />
                    </div>
                </div>

                {{-- Right Side: Content --}}
                <div class="flex-1 min-w-0">
                    
                    {{-- Meta Bar --}}
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-sm font-serif text-muted italic border-b border-rule pb-5 mb-8">
                        <div class="flex items-center gap-2">
                            <x-user-avatar :user="$post->user" size="xs" />
                            <span>By <a href="{{ route('profile.show', $post->user->username) }}" class="font-bold text-ink hover:text-accent">{{ $post->user->display_name }}</a></span>
                        </div>
                        <span class="opacity-30">&bull;</span>
                        <span>{{ $post->created_at->format('M d, Y') }}</span>
                        <span class="opacity-30">&bull;</span>
                        <span class="flex items-center gap-1"><x-icons.eye class="w-4 h-4 opacity-60"/> {{ number_format($post->view_count) }} Views</span>

                        <div class="ml-auto flex items-center gap-3">
                            <x-post.share-button />
                            <x-action-dropdown>
                                <x-report-button type="post" :id="$post->id" />
                                @can('update', $post)
                                    <a href="{{ route('posts.edit', $post) }}" class="block px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition-colors">Edit Entry</a>
                                @endcan
                                @can('delete', $post)
                                    <div class="my-1 border-t border-rule"></div>
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this entry permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-1.5 text-sm font-serif text-accent-warm hover:bg-aged transition-colors">Delete Entry</button>
                                    </form>
                                @endcan
                            </x-action-dropdown>
                        </div>
                    </div>

                    {{-- Warning if hidden --}}
                    @if($post->is_hidden)
                        <div class="mb-8 p-5 bg-aged border-l-2 border-accent-warm text-ink text-sm font-serif italic">
                            <strong>Notice:</strong> This entry is currently hidden from the public archive due to community guideline violations.
                        </div>
                    @endif

                    {{-- Main Body --}}
                    <div class="prose prose-lg dark:prose-invert max-w-none text-ink font-serif leading-relaxed mb-12">
                        <x-markdown :text="$post->body" />
                    </div>

                    {{-- Tags --}}
                    @if($post->tags->count() > 0)
                        <div class="flex flex-wrap gap-2 pt-6 border-t border-rule mb-12">
                            <span class="font-mono text-[10px] uppercase tracking-widest text-muted flex items-center mr-2">Filed under:</span>
                            @foreach($post->tags as $tag)
                                <x-tag-badge :tag="$tag" />
                            @endforeach
                        </div>
                    @endif

                </div>
            </div>

            {{-- Comments Section --}}
            <section class="mt-8 pt-10 border-t-4 border-double border-rule">
                <x-post.comment-section :post="$post" :comments="$comments" />
            </section>

        </article>

        {{-- Sidebar --}}
        <aside class="hidden lg:block lg:w-1/4 shrink-0">
            <x-post.sidebar-author :user="$post->user" />
        </aside>
    </div>
@endsection