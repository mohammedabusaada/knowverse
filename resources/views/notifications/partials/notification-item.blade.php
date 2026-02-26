@php
    $isUnread = !$notification->is_read;
@endphp

<div
    x-data="{ seen: {{ $notification->is_read ? 'true' : 'false' }} }"
    @if($isUnread)
    x-intersect.once.debounce.500ms="
        if (!seen) {
            seen = true;
            fetch('{{ route('notifications.read', $notification) }}', {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json' 
                }
            });
        }
    "
    @endif
    class="relative flex items-start gap-4 p-5 transition-all duration-300 
           {{ $isUnread ? 'bg-accent/5' : 'hover:bg-aged/30' }}">

    {{-- Unread Indicator --}}
    @if($isUnread)
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-accent"></div>
    @endif

    <div class="flex-shrink-0 pt-0.5">
        <div class="flex items-center justify-center w-8 h-8 rounded-sm border border-rule {{ $isUnread ? 'bg-paper text-accent' : 'bg-aged text-muted' }}">
            <span class="text-sm font-serif">{!! $notification->presenter()->icon() !!}</span>
        </div>
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex flex-col">
            <a href="{{ route('notifications.visit', $notification) }}"
                class="font-serif text-[15px] leading-relaxed {{ $isUnread ? 'font-bold text-ink' : 'text-muted' }} hover:text-accent transition-colors">
                {!! $notification->presenter()->message() !!}
            </a>

            <div class="flex flex-wrap items-center mt-2 space-x-3 font-mono text-[9px] uppercase tracking-widest text-muted">
                <span>{{ str_replace('_', ' ', $notification->type->value) }}</span>
                <span>&bull;</span>
                <span>{{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
</div>