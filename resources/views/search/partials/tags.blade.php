@if($tags->isEmpty())
    <x-search-empty icon="tag" message="No tags found matching your search." />
@else
    <div class="flex flex-wrap gap-3">
        @foreach($tags as $tag)
            <a href="{{ route('posts.index', ['tags' => [$tag->name]]) }}"
               class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-700 text-sm font-medium 
                      text-gray-700 dark:text-gray-300 hover:bg-blue-100 dark:hover:bg-blue-900/40 
                      hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                <span class="opacity-50 text-blue-500">#</span>{{ $tag->name }}
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $tags->links() }}
    </div>
@endif