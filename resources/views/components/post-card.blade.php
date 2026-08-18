@props(['post', 'compact' => false])

<a href="{{ route('posts.show', $post) }}" class="group grid grid-cols-[3rem_1fr] sm:grid-cols-[4rem_1fr_auto] gap-4 sm:gap-6 items-start p-4 sm:py-6 sm:px-4 border-b border-rule hover:bg-aged/10 hover:shadow-sm transition-all duration-300 relative block text-ink decoration-none first:border-t rounded-xl sm:rounded-none my-1 sm:my-0">
    
    {{-- Voting Metrics (Left Column) --}}
    <div class="font-mono text-xs text-muted pt-1 flex flex-col items-center justify-center bg-paper/50 sm:bg-transparent rounded-lg p-2 sm:p-0 border border-rule sm:border-transparent group-hover:border-accent/20 transition-colors">
        {{-- Interactive Upvote Icon --}}
        <svg class="w-5 h-5 mb-1 text-muted group-hover:text-accent group-hover:-translate-y-1 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
        </svg>
        <span class="text-lg font-bold text-ink group-hover:text-accent transition-colors">
            {{ $post->totalVotes() }}
        </span>
        <span class="text-[9px] uppercase tracking-widest mt-0.5 opacity-70">Votes</span>
    </div>

    {{-- Main Content Column --}}
    <div class="min-w-0 flex flex-col sm:flex-row gap-5">
        
        {{-- Optional Thumbnail --}}
        @if($post->image && !$compact)
            <div class="shrink-0 w-full sm:w-32 h-40 sm:h-24 rounded-md border border-rule bg-aged/30 flex justify-center items-center p-1 overflow-hidden shadow-sm">
                <img src="{{ $post->image_url }}" alt="Cover" class="max-w-full max-h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500">
            </div>
        @endif

        <div class="min-w-0 flex-1">
            {{-- Discussion Title --}}
            <h3 class="font-heading text-lg sm:text-xl font-bold leading-snug mb-2 group-hover:text-accent transition-colors">
                {{ request('search') ? Str::of($post->title)->highlight(request('search')) : $post->title }}
            </h3>
            
            {{-- Discussion Excerpt --}}
            @if(!$compact)
                <p class="text-[15px] text-muted line-clamp-2 leading-relaxed mb-4 font-serif">
                    @php
                        $plainText = strip_tags($post->body);
                        $plainText = preg_replace('/!\[.*?\]\(.*?\)/', '', $plainText);
                        $plainText = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $plainText);
                        $plainText = preg_replace('/(\*\*|__|\*|_)(.*?)\1/', '$2', $plainText);
                        $plainText = preg_replace('/#+\s*(.*?)\n/', '$1 ', $plainText);
                        $plainText = preg_replace('/(\$\$?)(.*?)\1/', '', $plainText);
                        $truncated = Str::limit(trim($plainText), 180);
                    @endphp
                    {{ request('search') ? Str::of($truncated)->highlight(request('search')) : $truncated }}
                </p>
            @endif

            {{-- Post Metadata & Mobile Tags --}}
            <div class="flex flex-col sm:flex-row sm:items-center gap-y-3 sm:gap-y-0 justify-between">
                
                {{-- Metadata (Author, Comments, Time) --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[13px] text-muted italic font-serif">
                    
                    {{-- Author --}}
                    <span class="flex items-center gap-1.5 not-italic font-semibold text-ink hover:underline">
                        <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" /></svg>
                        {{ $post->user->display_name }}
                    </span>
                    
                    <span class="opacity-50 hidden sm:inline">&bull;</span>
                    
                    {{-- Comments --}}
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" /></svg>
                        {{ $post->all_comments_count ?? $post->allComments()->count() }} <span class="hidden sm:inline">comments</span>
                    </span>

                    <span class="opacity-50 hidden sm:inline">&bull;</span>
                    
                    {{-- Time --}}
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 opacity-70" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                    
                    {{-- Pinned Flag --}}
                    @if($post->isPinned())
                        <span class="px-1.5 py-0.5 bg-accent/10 border border-accent/30 text-accent font-mono not-italic font-bold text-[9px] uppercase tracking-widest ml-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M16 9V4h1a1 1 0 0 0 0-2H7a1 1 0 0 0 0 2h1v5a3 3 0 0 1-3 3v2h5.97v7l1 1 1-1v-7H19v-2a3 3 0 0 1-3-3Z"/></svg>
                            Pinned
                        </span>
                    @endif

                    {{-- Visibility Flag --}}
                    @if($post->is_hidden)
                        <span class="px-1.5 py-0.5 bg-accent-warm/10 border border-accent-warm/30 text-accent-warm font-mono not-italic font-bold text-[9px] uppercase tracking-widest ml-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" /></svg>
                            Hidden
                        </span>
                    @endif
                </div>

                {{-- Associated Tags (Visible on Mobile inside the flex-col) --}}
                <div class="flex sm:hidden flex-wrap items-center gap-2 mt-2">
                    @foreach($post->tags->take(3) as $tag)
                        <span class="font-mono text-[9px] tracking-[0.1em] uppercase text-muted bg-paper border border-rule px-2 py-1 rounded-sm">
                            #{{ strtolower($tag->name) }}
                        </span>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    {{-- Associated Tags (Right Column on Desktop) --}}
    <div class="hidden sm:flex flex-col items-end gap-2 pt-1">
        @foreach($post->tags->take(3) as $tag)
            <span class="font-mono text-[9.5px] tracking-[0.1em] uppercase text-muted bg-paper border border-rule px-2.5 py-1 rounded-md shadow-sm group-hover:border-accent/30 group-hover:text-accent transition-colors">
                #{{ strtolower($tag->name) }}
            </span>
        @endforeach
    </div>
</a>