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

    $color = match($activity->action) {
        'post_created' => 'text-blue-500 bg-blue-50 dark:bg-blue-900/20',
        'comment_created' => 'text-green-500 bg-green-50 dark:bg-green-900/20',
        'vote_up' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20',
        'reputation_changed' => 'text-purple-500 bg-purple-50 dark:bg-purple-900/20',
        default => 'text-gray-500 bg-gray-50 dark:bg-gray-800',
    };
@endphp

<div class="flex gap-4 p-5 hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors border-b border-gray-100 dark:border-gray-700 last:border-0">
    {{-- Icon --}}
    <div class="flex-shrink-0">
        <div class="p-2 rounded-xl {{ $color }}">
            <x-dynamic-component :component="'icons.' . $icon" class="w-5 h-5" />
        </div>
    </div>

    <div class="flex-1">
        <div class="text-sm text-gray-900 dark:text-gray-100 leading-relaxed">
            {!! activity_description($activity) !!}
        </div>

        <div class="mt-1 text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-2">
            <x-icons.moon class="w-3 h-3 opacity-50" /> {{-- Just an example icon for time --}}
            {{ $activity->created_at->diffForHumans() }}
        </div>
    </div>
</div>