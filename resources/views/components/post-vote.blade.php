@props(['post'])

@php
    $user = auth()->user();
    $userVote = $user ? $post->votes()->where('user_id', $user->id)->value('value') : null;
    $score = $post->upvote_count - $post->downvote_count;
@endphp

<div x-data="voteComponent({ id: {{ $post->id }}, type: 'post', initialScore: {{ $score }}, initialVote: {{ $userVote ?? 'null' }} })"
     class="flex flex-col items-center gap-2 select-none">
    
    <button @click="voteAction(1)"
            :class="vote === 1 ? 'text-accent' : 'text-muted hover:text-ink'"
            class="p-1 transition-colors outline-none" title="Upvote">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 4l-8 10h16l-8-10z"/>
        </svg>
    </button>

    <span x-text="score" :class="scoreFlashClass" class="font-mono text-sm font-bold text-ink"></span>

    <button @click="voteAction(-1)"
            :class="vote === -1 ? 'text-accent-warm' : 'text-muted hover:text-ink'"
            class="p-1 transition-colors outline-none" title="Downvote">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 20l8-10H4l8 10z"/>
        </svg>
    </button>
</div>