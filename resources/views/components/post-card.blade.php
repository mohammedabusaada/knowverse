@props(['post', 'compact' => false])

<a href="{{ route('posts.show', $post) }}" class="group grid grid-cols-[3rem_1fr] sm:grid-cols-[3.5rem_1fr_auto] gap-4 sm:gap-6 items-start py-6 border-b border-rule hover:bg-accent/[0.03] dark:hover:bg-aged/5 transition-colors relative block text-ink decoration-none first:border-t">
    
    <div class="font-mono text-xs text-muted opacity-60 pt-1.5 text-right flex flex-col items-end">
        <span class="text-base font-bold text-ink">{{ $post->upvote_count - $post->downvote_count }}</span>
        <span class="text-[8px] uppercase tracking-widest mt-0.5">Votes</span>
    </div>

    {{-- Content --}}
    <div class="min-w-0">
        <h3 class="font-heading text-lg sm:text-xl font-bold leading-snug mb-2 group-hover:text-accent transition-colors">
            {{ request('q') ? Str::of($post->title)->highlight(request('q')) : $post->title }}
        </h3>
        
        @if(!$compact)
            <p class="text-[15px] text-muted line-clamp-2 leading-relaxed mb-3 font-serif">
                @php
                    $plainText = strip_tags($post->body);
                    $truncated = Str::limit($plainText, 180);
                @endphp
                {{ request('q') ? Str::of($truncated)->highlight(request('q')) : $truncated }}
            </p>
        @endif

        <div class="flex flex-wrap gap-4 text-[13.5px] text-muted italic font-serif">
            <span>{{ $post->user->display_name }}</span>
            <span>{{ $post->allComments()->count() }} replies</span>
            <span>{{ $post->created_at->diffForHumans() }}</span>
            
            @if($post->is_hidden)
                <span class="text-[#a65a38] font-mono not-italic font-bold text-[9px] uppercase tracking-widest">Hidden</span>
            @endif
        </div>
    </div>

    {{-- Tags (Right side on desktop) --}}
    <div class="hidden sm:flex flex-col items-end gap-2">
        @foreach($post->tags->take(2) as $tag)
            <span class="font-mono text-[9px] tracking-[0.1em] uppercase text-accent bg-accent/5 border border-accent/10 px-2 py-1 rounded-sm">
                {{ strtolower($tag->name) }}
            </span>
        @endforeach
    </div>
</a>