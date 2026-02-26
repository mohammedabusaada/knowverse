@props([
    'post',
    'q' => '',
])

@php
    $snippet = Str::limit(strip_tags($post->body), 220);
    $escaped = e($snippet);

    if ($q !== '') {
        $pattern = '/' . preg_quote($q, '/') . '/i';
        $highlighted = preg_replace(
            $pattern,
            '<mark class="bg-ink text-paper px-1 rounded-sm font-bold">$0</mark>',
            $escaped
        );
    } else {
        $highlighted = $escaped;
    }
@endphp

<a
    href="{{ route('posts.show', $post) }}"
    class="block py-6 border-b border-rule hover:bg-accent/[0.03] dark:hover:bg-aged/5 transition-colors group/link decoration-none text-ink first:border-t"
>
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-2">
        <div class="min-w-0">
            <h3 class="font-heading text-lg sm:text-xl font-bold leading-snug group-hover/link:text-accent transition-colors truncate">
                {{ $post->title }}
            </h3>
        </div>
    </div>

    {{-- Snippet --}}
    <p class="text-[15px] text-muted leading-relaxed mb-4 font-serif line-clamp-2">
        {!! $highlighted !!}
    </p>

    {{-- Meta Footer --}}
    <div class="flex flex-wrap items-center gap-4 text-[13.5px] text-muted italic font-serif">
        <span>By {{ $post->user->display_name }}</span>
        <span>{{ $post->created_at->diffForHumans() }}</span>
        
        <div class="ml-auto text-xs font-mono uppercase tracking-widest text-ink group-hover/link:text-accent transition-colors">
            View Entry &rarr;
        </div>
    </div>
</a>