@props(['type', 'points', 'date', 'source' => null, 'sourceType' => null])

@php
    $isPositive = $points > 0;
    $url = null;
    
    // 1. Map internal action types to scholarly terminology
    $displayTitle = match ($type) {
        'post_upvoted'          => 'Discussion Upvoted',
        'comment_upvoted'       => 'Comment Upvoted',
        'post_downvoted'        => 'Discussion Downvoted',
        'comment_downvoted'     => 'Comment Downvoted',
        'authors_pick_received' => 'Highlighted as Author\'s Pick',
        'authors_pick_awarded'  => 'Selected an Author\'s Pick',
        'post_created'          => 'Published a Discussion',
        'comment_created'       => 'Contributed a Comment',
        'post_upvoted_reverted'  => 'Discussion Upvote Retracted',
        'comment_upvoted_reverted' => 'Comment Upvote Retracted',
        'post_downvoted_reverted' => 'Discussion Downvote Retracted',
        'comment_downvoted_reverted' => 'Comment Downvote Retracted',
        'authors_pick_received_reverted' => 'Author\'s Pick Withdrawn',
        'authors_pick_awarded_reverted' => 'Author\'s Pick Selection Reversed',
        default                 => str_replace('_', ' ', ucfirst($type)),
    };

    // 2. Dynamically resolve the URL based on the Polymorphic Source
    if ($source) {
        if ($source instanceof \App\Models\Post) {
            $url = route('posts.show', $source);
        } elseif ($source instanceof \App\Models\Comment) {
            // Ensure we handle both the object and the potential ID reference
            $postId = $source->post_id ?? null;
            if ($postId) {
                $url = route('posts.show', $postId) . '#comment-' . $source->id;
            }
        }
    }
@endphp

<div class="flex items-center justify-between py-5 px-6 border-b border-rule hover:bg-aged/20 transition-colors last:border-b-0 group">
    <div class="flex items-start gap-4 min-w-0 pr-4">
        <div>
            <p class="font-serif text-[15px] text-ink font-bold">
                {{-- Display the refined scholarly title --}}
                <span>{{ $displayTitle }}</span>
                
                @if($url)
                    <a href="{{ $url }}" class="text-muted hover:text-accent font-normal italic ml-2 border-b border-transparent hover:border-accent transition-colors" aria-label="View source for {{ $displayTitle }}">
                        View source &rarr;
                    </a>
                @endif
            </p>
            @if($source instanceof \App\Models\Post)
                <p class="text-sm text-muted mt-1">{{ $source->title }}</p>
            @endif
            <p class="font-mono text-xs uppercase tracking-wide text-muted mt-1.5 opacity-70">
                {{ $date }}
            </p>
        </div>
    </div>
    
    {{-- Positive points in Ink (Black), Negative in Accent-Warm (Reddish) --}}
    <div class="font-mono text-lg shrink-0 {{ $isPositive ? 'text-ink font-bold' : 'text-accent-warm font-bold' }}">
        {{ $isPositive ? '+' : '' }}{{ $points }}
    </div>
</div>
