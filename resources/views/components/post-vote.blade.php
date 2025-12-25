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
    class="flex flex-col items-center w-12 gap-1 select-none"
>
    <button
        @click="voteAction(1)"
        :class="vote === 1 ? 'text-orange-500 bg-orange-50 dark:bg-orange-900/20' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
        class="p-2 rounded-full transition-all duration-200"
    >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12.781 2.375c-.381-.475-1.181-.475-1.562 0l-8 10A1.001 1.001 0 004 14h4v7a1 1 0 001 1h6a1 1 0 001-1v-7h4a1.001 1.001 0 00.781-1.625l-8-10z"/>
        </svg>
    </button>

    <span
        x-text="score"
        :class="scoreFlashClass"
        class="text-lg font-black text-gray-900 dark:text-gray-100 transition-all duration-300"
    ></span>

    <button
        @click="voteAction(-1)"
        :class="vote === -1 ? 'text-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700'"
        class="p-2 rounded-full transition-all duration-200"
    >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.219 21.625c.381.475 1.181.475 1.562 0l8-10A1.001 1.001 0 0020 10h-4V3a1 1 0 00-1-1H9a1 1 0 00-1 1v7H4a1.001 1.001 0 00-.781 1.625l8 10z"/>
        </svg>
    </button>
</div>