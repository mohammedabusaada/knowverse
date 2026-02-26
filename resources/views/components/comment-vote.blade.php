@props(['comment'])

@php
    $user = auth()->user();
    $userVote = $user ? $comment->votes()->where('user_id', $user->id)->value('value') : null;
    $score = $comment->upvote_count - $comment->downvote_count;
@endphp

<div x-data="voteComponent({ 
        id: {{ $comment->id }}, 
        type: 'comment', 
        initialScore: {{ $score }}, 
        initialVote: {{ $userVote ?? 'null' }} 
    })"
     class="flex flex-col items-center gap-1 select-none">
    
    <button @click="voteAction(1)"
            :class="vote === 1 ? 'text-accent' : 'text-muted hover:text-ink'"
            class="p-0.5 transition-colors outline-none" title="Upvote">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 4l-8 10h16l-8-10z"/>
        </svg>
    </button>

    <span x-text="score" 
          :class="scoreFlashClass" 
          class="font-mono text-xs font-bold text-ink">
    </span>

    <button @click="voteAction(-1)"
            :class="vote === -1 ? 'text-accent-warm' : 'text-muted hover:text-ink'"
            class="p-0.5 transition-colors outline-none" title="Downvote">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 20l8-10H4l8 10z"/>
        </svg>
    </button>
</div>