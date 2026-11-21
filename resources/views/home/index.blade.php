@extends('layouts.app')

@section('content')
<div class="home-page">
    <section class="home-hero" aria-labelledby="home-heading">
        <p class="eyebrow">A community for curious minds</p>
        <h1 id="home-heading">Good questions.<br><em>Better understanding.</em></h1>
        <p class="hero-description">Explore ideas, share what you know, and build knowledge together through thoughtful academic discussion.</p>
        <div class="flex flex-wrap gap-3 mt-6">
            <x-button primary :href="auth()->check() ? route('posts.create') : route('register')">{{ auth()->check() ? 'Start a discussion' : 'Join the community' }}</x-button>
            <x-button secondary :href="route('posts.index')">Explore discussions</x-button>
        </div>
        <dl class="community-metrics">
            <div><dt>Discussions</dt><dd>{{ number_format($stats['discussions']) }}</dd></div>
            <div><dt>Scholars</dt><dd>{{ number_format($stats['scholars']) }}</dd></div>
            <div><dt>Contributions</dt><dd>{{ number_format($stats['comments']) }}</dd></div>
        </dl>
    </section>
    <div class="home-columns">
        <section class="min-w-0" aria-labelledby="recent-heading">
            <div class="section-heading">
                <div><p class="eyebrow">The conversation continues</p><h2 id="recent-heading">Recent discussions</h2></div>
                <a href="{{ route('posts.index') }}" class="text-sm font-semibold underline underline-offset-4">View all</a>
            </div>
            <div class="discussion-feed">
                @forelse($recentPosts as $post)
                    <a class="discussion-preview" href="{{ route('posts.show', $post) }}">
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($post->tags->take(2) as $tag)<span class="topic-label">{{ $tag->name }}</span>@endforeach
                            @if($post->isPinned())<span class="topic-label">Pinned</span>@endif
                        </div>
                        <h3>{{ $post->title }}</h3>
                        <div class="discussion-meta">
                            <span class="flex items-center gap-2"><x-user-avatar :user="$post->user" size="xs" />{{ $post->user->display_name }}</span>
                            <span>{{ $post->created_at->diffForHumans() }}</span>
                            <span>{{ $post->comments_count }} {{ Str::plural('comment', $post->comments_count) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="empty-panel"><h3>Every conversation starts with a question.</h3><p>No discussions yet. Share an idea with the community.</p><x-button secondary :href="route('posts.create')">Start a discussion</x-button></div>
                @endforelse
            </div>
        </section>
        <aside class="home-rail" aria-label="Community discovery">
            <section class="rail-panel">
                <p class="eyebrow">Find your field</p><h2>Explore topics</h2>
                <div class="flex flex-wrap gap-2 mt-5">
                    @forelse($popularTags->take(8) as $tag)
                        <a class="topic-link" href="{{ route('tags.show', $tag->slug) }}">{{ $tag->name }} <span>{{ $tag->posts_count }}</span></a>
                    @empty<p class="text-muted">Topics will appear here as the community grows.</p>@endforelse
                </div>
                <a class="rail-link" href="{{ route('tags.index') }}">Browse all topics</a>
            </section>
            @auth
            <section class="rail-panel">
                <p class="eyebrow">Your academic standing</p>
                <h2>{{ auth()->user()->getAcademicStanding() }}</h2>
                <p class="text-muted mt-3">{{ number_format(auth()->user()->reputation_points) }} reputation points</p>
                <a class="rail-link" href="{{ route('profile.reputation', auth()->user()->username) }}">View your reputation</a>
            </section>
            @endauth
            <section class="rail-panel">
                <p class="eyebrow">People behind the ideas</p><h2>Top scholars</h2>
                <div class="space-y-5 mt-5">
                    @forelse($topScholars as $scholar)
                        <a href="{{ route('profile.show', $scholar->username) }}" class="flex items-center gap-3 group">
                            <x-user-avatar :user="$scholar" size="sm" />
                            <div class="min-w-0"><p class="font-semibold text-sm truncate group-hover:underline">{{ $scholar->display_name }}</p><p class="text-muted text-sm">{{ number_format($scholar->reputation_points) }} reputation</p></div>
                        </a>
                    @empty<p class="text-muted">No scholars ranked yet.</p>@endforelse
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
