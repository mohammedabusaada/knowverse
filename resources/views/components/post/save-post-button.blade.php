@props(['post'])

@php
    // Check if user is logged in and has saved the post
    $isSaved = Auth::check() && $post->isSavedBy(Auth::user());
@endphp

<button 
    x-data="savePost({{ $isSaved ? 'true' : 'false' }}, {{ $post->id }})"
    @click="toggle()"
    :disabled="loading"
    type="button"
    :title="isSaved ? 'Unsave Post' : 'Save Post'"
    class="mt-4 inline-flex items-center justify-center w-9 h-9 rounded-lg transition"
    :class="isSaved 
        ? 'text-yellow-500 bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/20' 
        : 'text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-800'"
>
    <svg x-show="isSaved" style="display: none;" class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
    </svg>

    <svg x-show="!isSaved" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
    </svg>
</button>