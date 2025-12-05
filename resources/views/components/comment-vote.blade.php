@props(['comment'])

@php
    $user = auth()->user();
    $userVote = $user ? $comment->votes()->where('user_id', $user->id)->value('value') : null;
    $score = $comment->upvote_count - $comment->downvote_count;
@endphp

<div 
    x-data="voteComponent({
        id: {{ $comment->id }},
        type: 'comment',
        initialScore: {{ $score }},
        initialVote: {{ $userVote ?? 'null' }}
    })"
    class="flex flex-col items-center w-8 select-none"
>

    <!-- UPVOTE -->
    <button 
        @click="voteAction(1)"
        :class="vote === 1 
            ? 'text-green-600 dark:text-green-400 font-bold'
            : 'text-gray-500'"
        class="transition transform hover:scale-125"
    >
        ▲
    </button>

    <!-- SCORE -->
    <span 
        x-text="score"
        class="font-semibold text-gray-800 dark:text-gray-100 text-sm transition"
        :class="scoreFlashClass"
    ></span>

    <!-- DOWNVOTE -->
    <button 
        @click="voteAction(-1)"
        :class="vote === -1 
            ? 'text-red-600 dark:text-red-400 font-bold'
            : 'text-gray-500'"
        class="transition transform hover:scale-125"
    >
        ▼
    </button>

</div>
