@if($tags->isEmpty())
    <div class="py-20 text-center border border-dashed border-rule bg-aged/10">
        <span class="block text-2xl mb-2 opacity-50 text-muted">✦</span>
        <h3 class="font-serif text-lg text-ink font-bold mb-1">No Topics Found</h3>
        <p class="font-serif text-sm text-muted italic">There are no records classified under this topic yet.</p>
    </div>
@else
    {{-- Grid layout to display the Tag Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-2 sm:p-4">
        @foreach($tags as $tag)
            <x-search-tag-card :tag="$tag" />
        @endforeach
    </div>

    <div class="mt-8 pt-4 border-t border-rule">
        {{ $tags->links() }}
    </div>
@endif