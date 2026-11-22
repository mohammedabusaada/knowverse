@extends('layouts.app')

@section('content')
<section class="discussions-page" aria-labelledby="discussions-heading">
    <header class="discussions-heading">
        <div>
            <p class="eyebrow">Exchange ideas. Build understanding.</p>
            <h1 id="discussions-heading">Discussions</h1>
            <p class="text-muted mt-3">Questions, perspectives, and research from the community.</p>
        </div>
        <x-button primary :href="route('posts.create')">Start a discussion</x-button>
    </header>

    <div class="discussions-toolbar">
        <p><strong>{{ number_format($posts->total()) }}</strong> {{ Str::plural('discussion', $posts->total()) }}<span class="text-muted"> · Pinned first, then newest</span></p>
        <a href="{{ route('search') }}" class="underline underline-offset-4">Search discussions</a>
    </div>

    <details class="discussion-filters" @if(count($selectedTags)) open @endif>
        <summary>Filter by topic @if(count($selectedTags))<span class="topic-label">{{ count($selectedTags) }} selected</span>@endif</summary>
        @include('posts.partials.filters')
    </details>

    @if(count($selectedTags))
        <div class="flex flex-wrap items-center gap-2 mb-6" aria-label="Active topic filters">
            @foreach($selectedTags as $tag)<span class="topic-link">{{ $tag }}</span>@endforeach
            <a href="{{ route('posts.index') }}" class="text-sm underline underline-offset-4 px-3 py-2">Clear filters</a>
        </div>
    @endif

    @if($posts->isEmpty())
        <div class="empty-panel">
            <h2 class="font-heading text-2xl">No discussions here yet</h2>
            <p>{{ $posts->total() ? 'This page has no results. Return to the first page to continue browsing.' : 'Try another topic, clear your filters, or start a new conversation.' }}</p>
            <x-button secondary :href="$posts->total() ? $posts->url(1) : route('posts.index')">{{ $posts->total() ? 'Back to first page' : 'View all discussions' }}</x-button>
        </div>
    @else
        <div class="discussion-list">
            @foreach($posts as $post)<x-post-card :post="$post" />@endforeach
        </div>
        <div class="mt-8">{{ $posts->onEachSide(1)->links('pagination.academic') }}</div>
    @endif
</section>
@endsection
