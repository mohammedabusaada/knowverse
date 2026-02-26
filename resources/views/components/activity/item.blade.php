@props(['activity'])

<div class="flex gap-5 py-6 border-b border-rule last:border-0 group hover:bg-aged/10 transition-colors px-2">
    {{-- Timeline dot --}}
    <div class="flex flex-col items-center pt-1.5">
        <span class="w-2.5 h-2.5 rounded-full bg-rule group-hover:bg-ink transition-colors"></span>
    </div>

    {{-- Content --}}
    <div class="flex-1 min-w-0">
        <div class="font-serif text-[15px] text-ink leading-relaxed">
            {!! activity_description($activity) !!}
            @if ($activity->details)
                <span class="text-muted italic ml-1">"{{ $activity->details }}"</span>
            @endif
        </div>

        <div class="font-mono text-[10px] uppercase tracking-[0.1em] text-muted mt-2">
            {{ $activity->created_at->diffForHumans() }}
        </div>
    </div>
</div>