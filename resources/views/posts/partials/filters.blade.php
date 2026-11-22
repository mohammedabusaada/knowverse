<div class="topic-filter-panel" x-data="{ searchQuery: '', topics: @js($tags->pluck('name')->values()) }">
    <form method="GET" action="{{ route('posts.index') }}">
        <label for="topic-filter-search" class="block text-sm font-semibold mb-2">Find a topic</label>
        <input id="topic-filter-search" type="search" x-model="searchQuery" placeholder="Search topics" class="w-full max-w-md border border-rule rounded-md px-4 py-3 bg-paper text-ink" />
        <fieldset class="mt-4">
            <legend class="sr-only">Choose topics to include</legend>
            <div class="topic-filter-options">
                @foreach($tags as $tag)
                    <label x-show="@js(mb_strtolower($tag->name)).includes(searchQuery.toLowerCase())" class="topic-filter-option">
                        <input type="checkbox" name="tags[]" value="{{ $tag->name }}" @checked(in_array($tag->name, $selectedTags)) />
                        <span>{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
            <p x-cloak x-show="!topics.some(topic => topic.toLowerCase().includes(searchQuery.toLowerCase()))" class="text-muted py-4" role="status">No topics match your search.</p>
        </fieldset>
        <div class="flex flex-wrap gap-3 mt-5">
            <x-button primary type="submit">Apply filters</x-button>
            <x-button secondary :href="route('posts.index')">Clear filters</x-button>
        </div>
    </form>
</div>
