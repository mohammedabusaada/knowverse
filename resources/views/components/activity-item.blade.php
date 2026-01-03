@props(['activity'])

@php
    $action = $activity->action;
    $target = $activity->target;
@endphp

<div class="flex gap-4 py-4 border-b dark:border-gray-700">

    {{-- Timeline dot --}}
    <div class="flex flex-col items-center">
        <span class="w-3 h-3 rounded-full bg-blue-600 mt-1"></span>
        <span class="flex-1 w-px bg-gray-300 dark:bg-gray-700"></span>
    </div>

    {{-- Content --}}
    <div class="flex-1">

        {{-- Message --}}
        <div class="text-sm text-gray-900 dark:text-gray-100">
            @switch($action)

                @case('post_created')
                    <span class="font-medium">Created a post</span>
                    @if($target)
                        —
                        <a href="{{ route('posts.show', $target) }}"
                           class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ Str::limit($target->title, 80) }}
                        </a>
                    @endif
                @break

                @case('comment_created')
                    <span class="font-medium">Commented on</span>
                    @if($target?->post)
                        <a href="{{ route('posts.show', $target->post) }}#comment-{{ $target->id }}"
                           class="text-blue-600 dark:text-blue-400 hover:underline">
                            {{ Str::limit($target->post->title, 80) }}
                        </a>
                    @endif
                @break

                @case('vote_up')
                    <span class="font-medium text-green-600">Upvoted</span>
                    {{ $target instanceof \App\Models\Post ? 'a post' : 'a comment' }}
                @break

                @case('vote_down')
                    <span class="font-medium text-red-600">Downvoted</span>
                    {{ $target instanceof \App\Models\Post ? 'a post' : 'a comment' }}
                @break

                @case('best_answer_selected')
                    <span class="font-medium">Selected a best answer</span>
                @break

                @case('reputation_changed')
                    <span class="font-medium">Reputation changed</span>
                    <span class="text-gray-500">
                        {{ $activity->details }}
                    </span>
                @break

                @case('login')
                    <span class="font-medium">Logged in</span>
                @break

                @case('logout')
                    <span class="font-medium">Logged out</span>
                @break

                @default
                    <span class="font-medium">
                        {{ Str::headline($action) }}
                    </span>
            @endswitch
        </div>

        {{-- Meta --}}
        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ $activity->created_at->diffForHumans() }}
        </div>

    </div>

</div>
