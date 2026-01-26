@php
$isUnread = !$notification->is_read;
@endphp

<div
    x-data="{ seen: {{ $notification->is_read ? 'true' : 'false' }} }"
    @if($isUnread)
    x-intersect.once="
        if (!seen) {
            seen = true;
            fetch('{{ route('notifications.read', $notification) }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
            });
        }
    "
    @endif
    class="relative flex items-start gap-4 p-4 transition-all duration-300 border-b border-gray-100 dark:border-gray-700/50 
           {{ $isUnread ? 'bg-indigo-50/30 dark:bg-indigo-900/10 border-l-4 border-indigo-500' : 'hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">

    <div class="flex-shrink-0 mt-1">
        <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $isUnread ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500' }}">
            <span class="text-xl">{!! $notification->presenter()->icon() !!}</span>
        </div>
    </div>

    <div class="flex-1 min-w-0">
        <div class="flex flex-col">
            <a href="{{ route('notifications.visit', $notification) }}"
                class="text-sm leading-relaxed {{ $isUnread ? 'font-bold text-gray-900 dark:text-white' : 'text-gray-600 dark:text-gray-400' }} hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                {!! $notification->presenter()->message() !!}
            </a>

            <div class="flex items-center mt-1.5 space-x-2 text-xs text-gray-400 dark:text-gray-500">
                <span class="font-medium capitalize">{{ str_replace('_', ' ', $notification->type->value) }}</span>
                <span>•</span>
                <span>{{ $notification->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>

    @if($isUnread)
    <div class="flex-shrink-0 ml-2">
        <span class="block w-2.5 h-2.5 bg-indigo-500 rounded-full shadow-sm shadow-indigo-200 dark:shadow-none"></span>
    </div>
    @endif
</div>