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
    class="flex flex-col items-center w-9 select-none"
>
    {{-- Upvote --}}
    <button
        @click="voteAction(1)"
        :class="vote === 1
            ? 'text-blue-700 dark:text-blue-300 bg-blue-600/10 border-blue-600/30 dark:bg-blue-500/20 dark:border-blue-500/30'
            : 'text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'"
        class="w-8 h-8 inline-flex items-center justify-center
               rounded-lg border transition
               hover:scale-[1.04]"
        title="Upvote"
    >
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 3l6 7H4l6-7z"/>
        </svg>
    </button>

    {{-- Score --}}
    <span
        x-text="score"
        :class="scoreFlashClass"
        class="my-1 text-sm font-semibold text-gray-800 dark:text-gray-100 transition"
    ></span>

    {{-- Downvote --}}
    <button
        @click="voteAction(-1)"
        :class="vote === -1
            ? 'text-blue-700 dark:text-blue-300 bg-blue-600/10 border-blue-600/30 dark:bg-blue-500/20 dark:border-blue-500/30'
            : 'text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'"
        class="w-8 h-8 inline-flex items-center justify-center
               rounded-lg border transition
               hover:scale-[1.04]"
        title="Downvote"
    >
        <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path d="M10 17l-6-7h12l-6 7z"/>
        </svg>
    </button>
</div>
