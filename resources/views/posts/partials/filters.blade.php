<div 
    x-data="{ open: {{ count($selectedTags) ? 'true' : 'false' }} }"
    class="bg-white dark:bg-gray-800 shadow rounded-xl border dark:border-gray-700"
>

    {{-- Header --}}
    <button
        type="button"
        @click="open = !open"
        class="w-full flex items-center justify-between px-6 py-4
               text-left focus:outline-none"
    >
        <div class="flex items-center gap-2">
            <h2 class="text-lg font-bold dark:text-white">
                Filter discussions
            </h2>

            @if(count($selectedTags))
                <span class="text-xs px-2 py-1 rounded-full
                             bg-blue-100 text-blue-700
                             dark:bg-blue-900 dark:text-blue-200">
                    {{ count($selectedTags) }} active
                </span>
            @endif
        </div>

        <svg 
            class="w-5 h-5 transform transition-transform duration-200
                   text-gray-600 dark:text-gray-300"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Body --}}
    <div
        x-show="open"
        x-transition
        x-cloak
        class="px-6 pb-6"
    >

        <form method="GET" action="{{ route('posts.index') }}" class="space-y-4">

            <div x-data="{ query: '' }" class="space-y-4">

                {{-- Search --}}
                <input
                    type="text"
                    x-model="query"
                    placeholder="Search topics..."
                    class="w-full px-4 py-2 rounded-lg border
                           bg-white dark:bg-gray-700
                           text-gray-800 dark:text-gray-200
                           border-gray-300 dark:border-gray-600
                           focus:ring focus:ring-blue-500"
                />

                {{-- Tags --}}
                <div class="grid grid-cols-2 gap-3">
                    @foreach ($tags as $tag)
                        <label
                            x-show="query === '' || '{{ strtolower($tag->name) }}'.includes(query.toLowerCase())"
                            class="flex items-center gap-2 p-2 rounded-lg cursor-pointer
                                   border dark:border-gray-600
                                   hover:bg-gray-100 dark:hover:bg-gray-700"
                        >
                            <input
                                type="checkbox"
                                name="tags[]"
                                value="{{ $tag->name }}"
                                class="rounded text-blue-600 focus:ring-blue-500"
                                {{ in_array($tag->name, $selectedTags) ? 'checked' : '' }}
                            >

                            <span class="text-gray-800 dark:text-gray-200">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

            </div>

            {{-- Actions --}}
            <div class="mt-4 flex gap-4">
                <button
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Apply filters
                </button>

                <a href="{{ route('posts.index') }}"
                   class="px-6 py-2 bg-gray-200 dark:bg-gray-700
                          text-gray-800 dark:text-gray-200
                          rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                    Clear
                </a>
            </div>

        </form>

    </div>
</div>
