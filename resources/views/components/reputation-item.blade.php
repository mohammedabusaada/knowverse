@props(['type', 'points', 'date', 'source' => null, 'sourceType' => null])

@php
    $isPositive = $points > 0;
    $url = null;
    if ($source) {
        if ($sourceType === \App\Models\Post::class) {
            $url = route('posts.show', $source);
        } elseif ($sourceType === \App\Models\Comment::class && isset($source->post_id)) {
            $url = route('posts.show', $source->post_id) . '#comment-' . $source->id;
        }
    }
@endphp

<div class="flex items-center justify-between py-5 border-b border-rule hover:bg-aged/20 transition-colors px-3">
    <div class="flex items-start gap-4">
        <div>
            <p class="font-serif text-base text-ink font-bold">
                <span class="capitalize">{{ str_replace('_', ' ', $type) }}</span>
                @if($url)
                    <a href="{{ $url }}" class="text-muted hover:text-ink font-normal italic ml-2 border-b border-transparent hover:border-ink transition-colors">
                        Ref &rarr;
                    </a>
                @endif
            </p>
            <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-muted mt-1.5">
                {{ $date }}
            </p>
        </div>
    </div>
    
    <div class="font-mono text-xl {{ $isPositive ? 'text-ink font-bold' : 'text-[#a65a38]' }}">
        {{ $isPositive ? '+' : '' }}{{ $points }}
    </div>
</div>