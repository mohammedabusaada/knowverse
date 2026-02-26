@if($tags->isEmpty())
    <x-search-empty icon="tag" message="No tags found matching your search." />
@else
    <div class="flex flex-wrap gap-3">
        @foreach($tags as $tag)
            <a href="{{ route('posts.index', ['tags' => [$tag->name]]) }}"
               class="px-5 py-2.5 rounded-xl border-2 border-gray-200 dark:border-gray-800 text-sm font-bold 
                      bg-gray-50 dark:bg-gray-900 text-black dark:text-white 
                      hover:border-black dark:hover:border-white hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition-all">
                <span class="opacity-50 mr-1">#</span>{{ $tag->name }}
            </a>
        @endforeach
    </div>

    <div class="mt-10">
        {{ $tags->links() }}
    </div>
@endif