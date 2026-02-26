@props([
    'placeholder' => 'Search posts, users, tags...',
    'action' => route('search'),
    'value' => null,
])

<div x-data="searchSuggestions('{{ $value }}')" class="relative w-full">
    <form action="{{ $action }}" method="GET" autocomplete="off">
        @foreach((array) request('tags', []) as $tag)
            <input type="hidden" name="tags[]" value="{{ $tag }}">
        @endforeach
        @if(request('type'))
            <input type="hidden" name="type" value="{{ request('type') }}">
        @endif

        <div class="relative group">
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-muted group-focus-within:text-ink transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="2"/>
                    <path stroke-width="2" stroke-linecap="round" d="M20 20l-3.5-3.5"/>
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
                class="w-full pl-10 pr-16 py-2 text-sm font-serif
                       bg-transparent border border-rule rounded-sm
                       text-ink placeholder:text-muted
                       focus:ring-0 focus:outline-none focus:border-ink
                       transition-all"
            >

            <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
                <template x-if="loading">
                    <svg class="animate-spin h-3 w-3 text-muted" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </template>
                <template x-if="!loading && !q">
                    <kbd class="hidden md:inline-flex px-1.5 py-0.5 text-[9px] font-mono text-muted border border-rule rounded-sm">
                        CTRL K
                    </kbd>
                </template>
            </div>
        </div>
    </form>

    <div x-show="open && (results.posts.length || results.users.length || results.tags.length)"
         x-transition x-cloak
         class="absolute z-50 mt-2 w-full bg-paper border border-rule shadow-xl rounded-sm overflow-hidden">
        
        <div class="max-h-[60vh] overflow-y-auto">
            <template x-if="results.posts.length">
                <div class="p-2 border-b border-rule">
                    <h3 class="px-3 py-1 text-[9px] font-mono text-muted uppercase tracking-widest">Posts</h3>
                    <template x-for="post in results.posts" :key="post.id">
                        <a :href="'/posts/' + post.id" @click="open = false"
                           class="flex items-center px-3 py-2 text-sm font-serif text-ink hover:bg-aged transition-colors">
                            <span class="opacity-40 mr-2">§</span>
                            <span x-text="post.title" class="truncate"></span>
                        </a>
                    </template>
                </div>
            </template>
            <template x-if="results.users.length">
                <div class="p-2 border-b border-rule">
                    <h3 class="px-3 py-1 text-[9px] font-mono text-muted uppercase tracking-widest">Users</h3>
                    <template x-for="user in results.users" :key="user.id">
                        <a :href="'/' + user.username" @click="open = false"
                           class="flex items-center px-3 py-2 text-sm hover:bg-aged transition-colors">
                            <div class="font-heading font-bold text-ink truncate mr-2" x-text="user.full_name ?? user.username"></div>
                            <div class="text-[10px] font-mono text-muted truncate" x-text="'@' + user.username"></div>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>