@props(['post', 'compact' => false])

<a href="{{ route('posts.show', $post) }}" class="group grid grid-cols-[3rem_1fr] sm:grid-cols-[3.5rem_1fr_auto] gap-4 sm:gap-6 items-start py-6 border-b border-rule hover:bg-aged/20 transition-colors relative block text-ink decoration-none first:border-t">
    
    {{-- Voting Metrics (Left Column) --}}
    <div class="font-mono text-xs text-muted pt-1 text-right flex flex-col items-end">
        <span class="text-lg font-bold text-ink group-hover:text-accent transition-colors">
            {{ $post->totalVotes() }}
        </span>
        <span class="text-[8px] uppercase tracking-widest mt-0.5 opacity-70">Votes</span>
    </div>

    {{-- Main Content Column --}}
    <div class="min-w-0 flex flex-col sm:flex-row gap-5">
        
        {{-- Optional Thumbnail --}}
        @if($post->image && !$compact)
            <div class="shrink-0 w-full sm:w-32 h-40 sm:h-24 rounded-sm border border-rule bg-aged/30 flex justify-center items-center p-1 overflow-hidden">
                <img src="{{ $post->image_url }}" alt="Cover" class="max-w-full max-h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500">
            </div>
        @endif

        <div class="min-w-0 flex-1">
            {{-- Discussion Title --}}
            <h3 class="font-heading text-lg sm:text-xl font-bold leading-snug mb-2 group-hover:text-accent transition-colors">
                {{ request('search') ? Str::of($post->title)->highlight(request('search')) : $post->title }}
            </h3>
            
            {{-- Discussion Excerpt --}}
            @if(!$compact)
                <p class="text-[15px] text-muted line-clamp-2 leading-relaxed mb-3 font-serif">
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

            {{-- Post Metadata --}}
            <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-[13px] text-muted italic font-serif">
                <span class="font-bold text-ink">{{ $post->user->display_name }}</span>
                <span class="opacity-50">&bull;</span>
                <span>{{ $post->all_comments_count ?? $post->allComments()->count() }} responses</span>
                <span class="opacity-50">&bull;</span>
                <span>{{ $post->created_at->diffForHumans() }}</span>
                
                {{-- Visibility Flag --}}
                @if($post->is_hidden)
                    <span class="px-1.5 py-0.5 bg-accent-warm/10 border border-accent-warm/30 text-accent-warm font-mono not-italic font-bold text-[9px] uppercase tracking-widest ml-2">
                        Hidden
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Associated Tags (Right Column on Desktop) --}}
    <div class="hidden sm:flex flex-col items-end gap-2">
        @foreach($post->tags->take(3) as $tag)
            <span class="font-mono text-[9px] tracking-[0.1em] uppercase text-muted bg-paper border border-rule px-2 py-1 rounded-sm group-hover:border-ink transition-colors">
                {{ strtolower($tag->name) }}
            </span>
        @endforeach
    </div>
</a>