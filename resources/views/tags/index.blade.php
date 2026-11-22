@extends('layouts.app')
@section('content')
<section class="topics-page" x-data="{ search: '', topics: @js($tags->pluck('name')->values()) }">
    <header class="mb-8">
        <p class="eyebrow">Find your field</p>
        <h1 class="font-heading text-4xl sm:text-5xl font-semibold tracking-tight">Explore topics</h1>
        <p class="text-muted mt-4">Discover conversations in your field and follow the subjects you care about.</p>
    </header>
    <label for="topic-search" class="block text-sm font-semibold mb-2">Filter topics on this page</label>
    <input id="topic-search" type="search" x-model="search" placeholder="Find a topic" class="w-full max-w-lg rounded-md border border-rule bg-paper text-ink px-4 py-3 mb-8" />
    <div class="topic-grid">
        @forelse($tags as $tag)
            @php $isFollowing = auth()->check() && auth()->user()->followedTags->contains($tag->id); @endphp
            <article class="rail-panel" x-show="@js(mb_strtolower($tag->name)).includes(search.toLowerCase())"
                x-data="{ following: @js($isFollowing), loading: false, error: '', async toggle() {
                    if (this.loading) return;
                    this.loading = true; this.error = '';
                    try {
                        const response = await fetch(@js(route('tags.follow', $tag)), { method: this.following ? 'DELETE' : 'POST', headers: { 'X-CSRF-TOKEN': @js(csrf_token()), 'Accept': 'application/json' } });
                        if (!response.ok) throw new Error();
                        this.following = !this.following;
                    } catch { this.error = 'Could not update this topic. Check your connection and account verification, then try again.'; }
                    finally { this.loading = false; }
                } }">
                <h2><a href="{{ route('tags.show', $tag->slug) }}" class="flex items-center gap-3 hover:underline"><x-tag-icon :slug="$tag->slug" classes="w-5 h-5 shrink-0" />{{ $tag->name }}</a></h2>
                <p class="text-muted text-sm leading-relaxed mt-4 mb-5">{{ $tag->description ?? 'Academic discussions and research related to '.$tag->name.'.' }}</p>
                <div class="flex flex-wrap items-center justify-between gap-3 mt-auto">
                    <a href="{{ route('tags.show', $tag->slug) }}" class="text-sm underline underline-offset-4">{{ $tag->posts_count }} {{ Str::plural('discussion', $tag->posts_count) }}</a>
                    @auth
                        <button type="button" @click="toggle()" :disabled="loading" :aria-pressed="following" class="min-h-11 px-4 py-2 border border-rule rounded-md text-sm font-semibold hover:bg-aged"><span x-text="loading ? 'Saving…' : (following ? 'Following' : 'Follow')"></span></button>
                    @else
                        <a href="{{ route('login') }}" class="min-h-11 px-4 py-2 border border-rule rounded-md text-sm font-semibold hover:bg-aged">Follow</a>
                    @endauth
                </div>
                <p x-cloak x-show="error" x-text="error" role="status" class="text-sm mt-4"></p>
            </article>
        @empty
            <div class="empty-panel"><h2 class="font-heading text-2xl">No topics yet</h2><p>Topics will appear here as the community grows.</p></div>
        @endforelse
    </div>
    @if($tags->count())<p x-cloak x-show="!topics.some(topic => topic.toLowerCase().includes(search.toLowerCase()))" role="status" class="empty-panel">No matching topics on this page. Try another term or browse the next page.</p>@endif
    <div class="mt-8">{{ $tags->onEachSide(1)->links('pagination.academic') }}</div>
</section>
@endsection
