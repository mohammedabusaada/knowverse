@props(['activity'])

@php
    $target = $activity->target;
    // Map actions to our custom icon set
    $icon = match($activity->action) {
        'post_created' => 'pencil',
        'comment_created' => 'chat',
        'vote_up' => 'arrow-up',
        'vote_down' => 'arrow-down',
        'reputation_changed' => 'chart',
        default => 'user',
    };
@endphp

<div class="flex gap-5 p-6 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors border-b-2 border-gray-100 dark:border-gray-800 last:border-0 group">
    {{-- Icon --}}
    <div class="flex-shrink-0 mt-1">
        <div class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-black dark:text-white border border-gray-200 dark:border-gray-700 group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition-colors">
            <x-dynamic-component :component="'icons.' . $icon" class="w-5 h-5" />
        </div>
    </div>

    <div class="flex-1 min-w-0">
        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-relaxed">
            {!! activity_description($activity) !!}
        </div>

        <div class="mt-2 text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-2">
            {{ $activity->created_at->diffForHumans() }}
        </div>
    </div>
</div>