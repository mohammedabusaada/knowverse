@if($posts->isEmpty())
    <div class="py-20 text-center border border-dashed border-rule bg-aged/10">
        <span class="block text-2xl mb-2 opacity-50 text-muted">✦</span>
        <h3 class="font-serif text-lg text-ink font-bold mb-1">No Discussions Found</h3>
        <p class="font-serif text-sm text-muted italic">Try adjusting your keywords.</p>
    </div>
@else
    <div class="flex flex-col">
        @foreach($posts as $post)
            <x-post-card :post="$post" compact />
        @endforeach
    </div>

    <div class="mt-8 pt-4 border-t border-rule">
        {{ $posts->links() }}
    </div>
@endif