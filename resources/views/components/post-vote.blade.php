@props(['post'])

@php
    $user = auth()->user();
    $userVote = $user ? $post->votes()->where('user_id', $user->id)->value('value') : null;
    $score = $post->upvote_count - $post->downvote_count;
@endphp

<div
    x-data="voteComponent({
        id: {{ $post->id }},
        type: 'post',
        initialScore: {{ $score }},
        initialVote: {{ $userVote ?? 'null' }}
    })"
    class="flex flex-col items-center w-10 gap-1 select-none"
>

    <!-- UPVOTE -->
    <button
        @click="voteAction(1)"
        :class="vote === 1
            ? 'text-green-600 dark:text-green-400 font-bold'
            : 'text-gray-500'"
        class="p-1 rounded transition hover:scale-125 hover:bg-gray-200 dark:hover:bg-gray-700"
    >
        ▲
    </button>

    <!-- SCORE -->
    <span
        x-text="score"
        :class="scoreFlashClass"
        class="font-bold text-gray-900 dark:text-gray-100 transition"
    ></span>

    <!-- DOWNVOTE -->
    <button
        @click="voteAction(-1)"
        :class="vote === -1
            ? 'text-red-600 dark:text-red-400 font-bold'
            : 'text-gray-500'"
        class="p-1 rounded transition hover:scale-125 hover:bg-gray-200 dark:hover:bg-gray-700"
    >
        ▼
    </button>

</div>
