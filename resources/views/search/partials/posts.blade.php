@if($posts->isEmpty())
    <x-search-empty icon="search" message="No discussions match your search query." />
@else
    <div class="space-y-6">
        @foreach($posts as $post)
            {{-- We pass 'q' as 'highlight' so the post card can highlight the search term --}}
            <x-post-card :post="$post" :highlight="$q ?? null" compact />
        @endforeach
    </div>

    <div class="mt-8">
        {{ $posts->links() }}
    </div>
@endif