@props(['post', 'q' => ''])

@php
    $snippet = Str::limit(strip_tags($post->body), 200);
    $escaped = e($snippet);

    if ($q !== '') {
        $pattern = '/' . preg_quote($q, '/') . '/i';
        $highlighted = preg_replace(
            $pattern,
            '<mark class="bg-ink text-paper px-0.5 rounded-sm font-bold">$0</mark>',
            $escaped
        );
    } else {
        $highlighted = $escaped;
    }
@endphp

<a href="{{ route('posts.show', $post) }}"
    class="block py-6 border-b border-rule hover:bg-aged/20 transition-colors group/link decoration-none text-ink first:border-t"
>
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="min-w-0">
            <h3 class="font-heading text-xl font-bold leading-snug group-hover/link:text-accent transition-colors">
    {{ $post->title }}
</h3>
        </div>
    </div>

    <p class="text-[15px] text-muted leading-relaxed mb-4 font-serif italic">
        "{!! $highlighted !!}"
    </p>

    <div class="flex flex-wrap items-center gap-4 text-[13px] text-muted font-serif">
        <span class="font-bold text-ink">{{ $post->user->display_name }}</span>
        <span class="opacity-30">&bull;</span>
        <span>{{ $post->created_at->format('M d, Y') }}</span>
        
        <div class="ml-auto font-mono text-[9px] uppercase tracking-widest text-muted group-hover/link:text-ink transition-colors">
            Read More &rarr;
        </div>
    </div>
</a>