@props([
    'placeholder' => 'Search posts, users, tags...',
    'action' => route('search'),
    'value' => null,
])

<div x-data="searchSuggestions('{{ $value }}')" class="relative w-full">
    <form action="{{ $action }}" method="GET" autocomplete="off">

        {{-- Keep filters --}}
        @foreach((array) request('tags', []) as $tag)
            <input type="hidden" name="tags[]" value="{{ $tag }}">
        @endforeach

        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif

        <div class="relative group">

            <!-- Search Icon -->
            <div class="absolute left-4 top-1/2 -translate-y-1/2
                        text-gray-400 group-focus-within:text-black
                        dark:group-focus-within:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="2.5"/>
                    <path stroke-width="2.5" stroke-linecap="round" d="M20 20l-3.5-3.5"/>
                </svg>
            </div>

            <!-- Input -->
            <input
                type="text"
                name="q"
                x-model="q"
                @input.debounce.300ms="fetch"
                @focus="q.length >= 2 && fetch()"
                @click.away="open = false"
                @keydown.escape="open = false"
                placeholder="{{ $placeholder }}"
                class="w-full pl-12 pr-16 py-3 text-sm
                       rounded-2xl
                       border border-gray-300 dark:border-gray-700
                       bg-white dark:bg-gray-800
                       text-gray-900 dark:text-gray-100
                       shadow-sm
                       focus:ring-2 focus:ring-black/30
                       dark:focus:ring-white/20
                       focus:border-black dark:focus:border-white
                       outline-none transition"
            >

            <!-- Right Side -->
            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2">

                <template x-if="loading">
                    <svg class="animate-spin h-4 w-4 text-gray-500"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>

                <template x-if="!loading && !q">
                    <kbd class="hidden md:inline-flex px-1.5 py-0.5
                               text-[10px] font-bold
                               text-gray-400
                               bg-gray-100 dark:bg-gray-700
                               border border-gray-300 dark:border-gray-600
                               rounded">
                        CTRL K
                    </kbd>
                </template>
            </div>
        </div>
    </form>

    <!-- Suggestions -->
    <div
        x-show="open && (results.posts.length || results.users.length || results.tags.length)"
        x-transition
        x-cloak
        class="absolute z-50 mt-2 w-full
               bg-white dark:bg-gray-800
               border border-gray-200 dark:border-gray-700
               rounded-xl shadow-xl overflow-hidden"
    >
        <div class="max-h-[60vh] overflow-y-auto">

            <!-- Posts -->
            <template x-if="results.posts.length">
                <div class="p-2 border-b dark:border-gray-700">
                    <h3 class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase">
                        Posts
                    </h3>

                    <template x-for="post in results.posts" :key="post.id">
                        <a :href="'/posts/' + post.id"
                           @click="open = false"
                           class="flex items-center px-3 py-2 text-sm
                                  text-gray-700 dark:text-gray-300
                                  hover:bg-gray-100 dark:hover:bg-gray-700
                                  rounded-lg transition">
                            <svg class="w-4 h-4 mr-3 opacity-40"
                                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-width="2"
                                      d="M9 12h6m-6 4h6M7 3h5l5 5v11a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                            <span x-text="post.title"></span>
                        </a>
                    </template>
                </div>
            </template>

            <!-- Users -->
            <template x-if="results.users.length">
                <div class="p-2 border-b dark:border-gray-700">
                    <h3 class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase">
                        Users
                    </h3>

                    <template x-for="user in results.users" :key="user.id">
                        <a :href="'/' + user.username"
                           @click="open = false"
                           class="flex items-center px-3 py-2 text-sm
                                  hover:bg-gray-100 dark:hover:bg-gray-700
                                  rounded-lg transition">
                            <div class="w-7 h-7 mr-3 rounded-full
                                        bg-gray-300 dark:bg-gray-600
                                        flex items-center justify-center
                                        text-xs font-bold">
                                <span x-text="user.username.charAt(0).toUpperCase()"></span>
                            </div>

                            <div>
                                <div class="font-medium"
                                     x-text="user.full_name ?? user.username"></div>
                                <div class="text-[10px] opacity-50"
                                     x-text="'@' + user.username"></div>
                            </div>
                        </a>
                    </template>
                </div>
            </template>

            <!-- Tags -->
            <template x-if="results.tags.length">
                <div class="p-2">
                    <h3 class="px-3 py-1 text-[10px] font-bold text-gray-400 uppercase">
                        Tags
                    </h3>

                    <div class="flex flex-wrap gap-2 p-2">
                        <template x-for="tag in results.tags" :key="tag.id">
                            <a :href="'/search?q=' + encodeURIComponent(tag.name) + '&type=tags'"
                               @click="open = false"
                               class="px-2 py-1 text-xs
                                      bg-gray-100 dark:bg-gray-700
                                      text-gray-600 dark:text-gray-300
                                      rounded hover:bg-gray-200 dark:hover:bg-gray-600
                                      transition"
                               x-text="'#' + tag.name"></a>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-900/50
                    px-4 py-2 text-[10px] text-gray-400
                    flex justify-between border-t dark:border-gray-700">
            <span>Press <kbd class="font-bold">Enter</kbd> for results</span>
            <span><kbd class="font-bold">Esc</kbd> to close</span>
        </div>
    </div>
</div>
