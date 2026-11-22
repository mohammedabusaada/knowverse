@props(['post', 'compact' => false])
@php
    $commentCount = $post->all_comments_count ?? $post->allComments()->count();
    $excerpt = strip_tags($post->body);
    $excerpt = preg_replace('/!\[.*?\]\(.*?\)/', '', $excerpt);
    $excerpt = preg_replace('/\[(.*?)\]\(.*?\)/', '$1', $excerpt);
    $excerpt = preg_replace('/(\*\*|__|\*|_)(.*?)\1/', '$2', $excerpt);
    $excerpt = preg_replace('/#+\s*(.*?)\n/', '$1 ', $excerpt);
    $excerpt = preg_replace('/(\$\$?)(.*?)\1/', '', $excerpt);
    $excerpt = Str::limit(trim($excerpt), 180);
@endphp
<a href="{{ route('posts.show', $post) }}" class="discussion-card group">
    <div class="discussion-card-score"><strong>{{ $post->totalVotes() }}</strong><span>votes</span></div>
    <div class="min-w-0">
        <div class="flex flex-wrap gap-2 mb-3">
            @foreach($post->tags->take(3) as $tag)<span class="topic-label">{{ $tag->name }}</span>@endforeach
            @if($post->isPinned())<span class="topic-label font-semibold">Pinned</span>@endif
            @if($post->is_hidden)<span class="topic-label font-semibold">Hidden</span>@endif
        </div>
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="min-w-0 flex-1">
                <h3 class="font-heading text-xl font-semibold leading-snug group-hover:underline underline-offset-4">{{ request('search') ? Str::of($post->title)->highlight(request('search')) : $post->title }}</h3>
                @unless($compact)<p class="text-muted text-base leading-relaxed line-clamp-2 mt-3">{{ request('search') ? Str::of($excerpt)->highlight(request('search')) : $excerpt }}</p>@endunless
            </div>
            @if($post->image && !$compact)<img src="{{ $post->image_url }}" alt="" loading="lazy" class="w-full sm:w-28 h-32 sm:h-24 object-cover rounded-md border border-rule shrink-0" />@endif
        </div>
        <div class="discussion-meta mt-4">
            <span class="font-medium text-ink">{{ $post->user->display_name }}</span>
            <span>{{ $commentCount }} {{ Str::plural('comment', $commentCount) }}</span>
            <time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
        </div>
    </div>
</a>
