<div
    x-data="{ open: {{ count($selectedTags) ? 'true' : 'false' }}, query: '' }"
    class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl"
>
    {{-- Header --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full flex items-center justify-between px-5 py-4 text-left
               border-b border-gray-200 dark:border-gray-800
               hover:bg-gray-50 dark:hover:bg-gray-950/40
               transition"
    >
        <div class="flex items-center gap-3 min-w-0">
            <h2 class="text-sm font-semibold tracking-wide text-gray-900 dark:text-gray-100">
                Filter discussions
            </h2>

            @if(count($selectedTags))
                <span class="shrink-0 text-[11px] px-2 py-0.5 rounded-full
                             bg-gray-100 text-gray-700 border border-gray-200
                             dark:bg-gray-950 dark:text-gray-200 dark:border-gray-800">
                    {{ count($selectedTags) }} active
                </span>
            @endif
        </div>

        <svg
            class="w-5 h-5 shrink-0 transform transition-transform duration-200 text-gray-500 dark:text-gray-400"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Body --}}
    <div x-show="open" x-transition x-cloak class="px-5 py-4">
        <form method="GET" action="{{ route('posts.index') }}" class="space-y-4">

            {{-- Search --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 dark:text-gray-300 mb-2">
                    Search tags
                </label>

                <input
                    type="text"
                    x-model="query"
                    placeholder="Type to filter tags..."
                    class="w-full px-3 py-2 rounded-lg text-sm
                           bg-white dark:bg-gray-950
                           border border-gray-200 dark:border-gray-800
                           text-gray-900 dark:text-gray-100
                           placeholder:text-gray-400 dark:placeholder:text-gray-500
                           focus:outline-none focus:ring-2 focus:ring-blue-600/25 focus:border-blue-600
                           transition"
                />
            </div>

            {{-- Tags (scroll) --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">
                        Tags
                    </span>
                    <span class="text-[11px] text-gray-500 dark:text-gray-400">
                        {{ $tags->count() }}
                    </span>
                </div>

                <div class="max-h-64 overflow-y-auto pr-1 grid grid-cols-1 gap-2">
                    @foreach ($tags as $tag)
                        @php
                            $tagLower = strtolower($tag->name);
                            $checked = in_array($tag->name, $selectedTags);
                        @endphp

                        <label
                            x-show="query === '' || '{{ $tagLower }}'.includes(query.toLowerCase())"
                            class="flex items-center gap-2 p-2 rounded-lg cursor-pointer
                                   border border-gray-200 dark:border-gray-800
                                   bg-gray-50 dark:bg-gray-950
                                   hover:bg-white dark:hover:bg-gray-900
                                   transition"
                        >
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->name }}"
                                class="rounded border-gray-300 dark:border-gray-700
                                       text-blue-600 focus:ring-blue-600/25"
                                {{ $checked ? 'checked' : '' }}
                            />

                            <span class="text-sm text-gray-800 dark:text-gray-200 truncate">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                {{-- Empty after filter --}}
                <div
                    x-show="query !== '' && $el.previousElementSibling && $el.previousElementSibling.querySelectorAll('label:not([style*=display\\: none])').length === 0"
                    class="mt-3 text-sm text-gray-500 dark:text-gray-400"
                    style="display:none;"
                >
                    No tags match your search.
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center
                           px-4 py-2 rounded-lg text-sm font-semibold
                           bg-blue-600 text-white hover:bg-blue-700
                           dark:bg-blue-500 dark:hover:bg-blue-600
                           transition"
                >
                    Apply
                </button>

                <a
                    href="{{ route('posts.index') }}"
                    class="inline-flex items-center justify-center
                           px-4 py-2 rounded-lg text-sm font-semibold
                           bg-white dark:bg-gray-950
                           border border-gray-200 dark:border-gray-800
                           text-gray-700 dark:text-gray-200
                           hover:bg-gray-50 dark:hover:bg-gray-900
                           transition"
                >
                    Clear
                </a>
            </div>

        </form>
    </div>
</div>
