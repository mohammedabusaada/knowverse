@props(['tag'])

<a href="{{ route('tags.show', $tag->slug) }}"
   class="group flex items-center justify-between p-4 bg-paper border border-rule rounded-sm hover:border-ink transition-colors">
    <div class="flex items-center gap-4">
        <div class="w-10 h-10 bg-aged border border-rule rounded-sm flex items-center justify-center text-muted group-hover:text-ink transition-colors">
            <span class="font-serif text-lg opacity-40">§</span>
        </div>
        <div class="min-w-0">
            <span class="font-mono text-xs uppercase tracking-[0.1em] text-ink font-bold group-hover:text-accent transition-colors">
                {{ strtolower($tag->name) }}
            </span>
            <p class="font-mono text-[9px] text-muted uppercase tracking-widest mt-0.5">
                {{ number_format($tag->posts_count ?? 0) }} documented records
            </p>
        </div>
    </div>
    <span class="text-muted group-hover:text-ink transition-colors text-sm">&rarr;</span>
</a>