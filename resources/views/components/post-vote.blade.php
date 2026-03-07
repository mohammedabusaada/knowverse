@props(['post'])

@php
    $user = auth()->user();
    // Architectural check: Prevent authors from inflating their own metrics
    $isAuthor = $user ? $user->id === $post->user_id : false;
    $userVote = $user ? $post->votes()->where('user_id', $user->id)->value('value') : 'null';
    $score = $post->upvote_count - $post->downvote_count;
@endphp

<div x-data="voteComponent({ id: {{ $post->id }}, type: 'post', initialScore: {{ $score }}, initialVote: {{ $userVote ?? 'null' }} })"
     class="flex flex-col items-center gap-2 select-none">
    
    <button 
        @if($isAuthor) 
            disabled title="Authors cannot evaluate their own discussions." class="p-1 opacity-30 cursor-not-allowed text-muted"
        @else
            @click="@auth @if(auth()->user()->hasVerifiedEmail()) voteAction(1) @else window.location.href='{{ route('verification.notice') }}' @endif @else window.location.href='{{ route('login') }}' @endauth"
            :class="vote === 1 ? 'text-ink' : 'text-muted hover:text-ink'"
            class="p-1 transition-colors outline-none focus:outline-none" title="Upvote"
        @endif
    >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 4l-8 10h16l-8-10z"/></svg>
    </button>

    <span x-text="score" 
          :class="scoreFlashClass" 
          class="font-mono text-sm font-bold text-ink inline-block transition-all duration-200"></span>

    <button 
        @if($isAuthor) 
            disabled title="Authors cannot evaluate their own discussions." class="p-1 opacity-30 cursor-not-allowed text-muted"
        @else
            @click="@auth @if(auth()->user()->hasVerifiedEmail()) voteAction(-1) @else window.location.href='{{ route('verification.notice') }}' @endif @else window.location.href='{{ route('login') }}' @endauth"
            :class="vote === -1 ? 'text-accent-warm' : 'text-muted hover:text-accent-warm'"
            class="p-1 transition-colors outline-none focus:outline-none" title="Downvote"
        @endif
    >
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 20l8-10H4l8 10z"/></svg>
    </button>
</div>