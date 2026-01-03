@php
    $target = $activity->target;
@endphp

<div class="flex gap-4 p-5">

    {{-- Icon --}}
    <div class="flex-shrink-0 mt-1">
        @switch($activity->action)

            @case('post_created')
                <span class="text-blue-600">📝</span>
                @break

            @case('comment_created')
                <span class="text-green-600">💬</span>
                @break

            @case('vote_up')
                <span class="text-emerald-600">⬆️</span>
                @break

            @case('vote_down')
                <span class="text-red-600">⬇️</span>
                @break

            @case('best_answer_selected')
                <span class="text-yellow-500">⭐</span>
                @break

            @case('reputation_changed')
                <span class="text-purple-600">🏆</span>
                @break

            @default
                <span class="text-gray-400">•</span>
        @endswitch
    </div>

    {{-- Content --}}
    <div class="flex-1">

        {{-- Description --}}
        <div class="text-sm text-gray-900 dark:text-gray-100">
            {!! activity_description($activity) !!}
        </div>

        {{-- Timestamp --}}
        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ $activity->created_at->diffForHumans() }}
        </div>

    </div>

</div>
