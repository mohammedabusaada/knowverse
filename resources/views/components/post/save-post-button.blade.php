@props(['post'])

@php
    /** @var bool $isSaved Determine if the post is already bookmarked by the authenticated user */
    $isSaved = Auth::check() && $post->isSavedBy(Auth::user());
@endphp

<div x-data="savePost({{ $isSaved ? 'true' : 'false' }}, {{ $post->id }})">
    <button 
        @click="@auth @if(auth()->user()->hasVerifiedEmail()) toggle() @else window.location.href='{{ route('verification.notice') }}' @endif @else window.location.href='{{ route('login') }}' @endauth"
        
        :disabled="loading"
        type="button"
        :title="isSaved ? 'Unsave Post' : 'Save Post'"
        class="mt-3 inline-flex items-center justify-center w-10 h-10 rounded-lg border-2 transition-all"
        :class="isSaved 
            ? 'border-black text-white bg-black dark:border-white dark:text-black dark:bg-white' 
            : 'border-transparent text-gray-400 hover:text-black hover:border-gray-200 hover:bg-gray-50 dark:hover:text-white dark:hover:border-gray-700 dark:hover:bg-gray-800'"
    >
        <svg x-show="isSaved" style="display: none;" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
        </svg>

        <svg x-show="!isSaved" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
        </svg>
    </button>
</div>