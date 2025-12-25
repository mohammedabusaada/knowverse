@props([
    'placeholder' => 'Search posts, users, tags...',
    'action' => route('search'),
    'value' => null,
])

<div x-data="searchSuggestions('{{ $value }}')" class="relative w-full">
    <form action="{{ $action }}" method="GET" autocomplete="off">
        {{-- Pass existing tags through the search bar form --}}
        @foreach((array) request('tags', []) as $tag)
            <input type="hidden" name="tags[]" value="{{ $tag }}">
        @endforeach
        
        {{-- Pass existing type if on results page --}}
        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif

        <div class="relative group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="2.5"/>
                    <path stroke-width="2.5" stroke-linecap="round" d="M20 20l-3.5-3.5"/>
                </svg>
            </div>

            <input
                type="text"
                name="q"
                x-model="q"
                @input.debounce.300ms="fetch"
                @focus="q.length >= 2 && fetch()"
                @click.away="open = false"
                @keydown.escape="open = false"
                placeholder="{{ $placeholder }}"
                class="w-full pl-12 pr-16 py-3 rounded-2xl text-sm
                       border border-gray-200 dark:border-gray-700
                       bg-white dark:bg-gray-800
                       text-gray-800 dark:text-gray-200
                       shadow-sm transition-all
                       focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none"
            >

            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">
                <template x-if="loading">
                    <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                
                <template x-if="!loading && !q">
                    <kbd class="hidden md:inline-flex px-1.5 py-0.5 text-[10px] font-bold text-gray-400 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded">
                        CTRL K
                    </kbd>
                </template>
            </div>
        </div>
    </form>

    {{-- Suggestions Dropdown (Remains the same) --}}
    <div
        x-show="open && (results.posts.length || results.users.length || results.tags.length)"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-cloak
        class="absolute z-50 mt-2 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden"
    >
        <div class="max-h-[60vh] overflow-y-auto">
            <template x-if="results.posts.length">
                <div class="p-2 border-b dark:border-gray-700">
                    <h3 class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Posts</h3>
                    <template x-for="post in results.posts" :key="post.id">
                        <a :href="'/posts/' + post.id" @click="open = false" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition">
                            <svg class="w-4 h-4 mr-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span x-text="post.title"></span>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="results.users.length">
                <div class="p-2 border-b dark:border-gray-700">
                    <h3 class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Users</h3>
                    <template x-for="user in results.users" :key="user.id">
                        <a :href="'/' + user.username" @click="open = false" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400 rounded-lg transition">
                            <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 mr-3 flex items-center justify-center text-[10px] font-bold" x-text="user.username.charAt(0).toUpperCase()"></div>
                            <div>
                                <div class="font-medium" x-text="user.full_name ?? user.username"></div>
                                <div class="text-[10px] opacity-50" x-text="'@' + user.username"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </template>

            <template x-if="results.tags.length">
                <div class="p-2">
                    <h3 class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Tags</h3>
                    <div class="flex flex-wrap gap-2 p-2">
                        <template x-for="tag in results.tags" :key="tag.id">
                            <a :href="'/search?q=' + encodeURIComponent(tag.name) + '&type=tags'" @click="open = false" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-400 rounded hover:bg-blue-100 dark:hover:bg-blue-900/40 hover:text-blue-600 dark:hover:text-blue-300 transition" x-text="'#' + tag.name"></a>
                        </template>
                    </div>
                </div>
            </template>
        </div>
        <div class="bg-gray-50 dark:bg-gray-900/50 px-4 py-2 text-[10px] text-gray-400 flex justify-between border-t dark:border-gray-700">
            <span>Press <kbd class="font-bold uppercase">Enter</kbd> for all results</span>
            <span><kbd class="font-bold uppercase">Esc</kbd> to close</span>
        </div>
    </div>
</div>