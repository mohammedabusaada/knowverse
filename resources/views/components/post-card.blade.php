@props(['post', 'compact' => false])

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors shadow-sm overflow-visible mb-4">
    <div class="p-4 md:p-5 flex gap-4">

        {{-- Sidebar Stats (Desktop) --}}
        <div class="hidden md:flex flex-col items-center w-12 text-gray-500 dark:text-gray-400">
            <div class="text-center">
                <span class="block text-lg font-bold text-gray-900 dark:text-white">
                    {{ $post->upvote_count - $post->downvote_count }}
                </span>
                <span class="text-[10px] uppercase font-bold tracking-tighter">Votes</span>
            </div>
            <div class="mt-4 text-center opacity-60">
                <span class="block text-sm font-semibold">{{ $post->allComments()->count() }}</span>
                <span class="text-[10px] uppercase font-bold tracking-tighter">Replies</span>
            </div>
        </div>

        <div class="flex-1">
            {{-- Top Row --}}
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <x-user-avatar :user="$post->user" size="xs" />
                    <x-user-hover-card :user="$post->user" />
                    <span>•</span>
                    <span>{{ $post->created_at->diffForHumans() }}</span>

                    @if($post->is_hidden)
                        <span class="ml-2 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border border-red-200 dark:border-red-800">
                            Hidden
                        </span>
                    @endif
                </div>

                <x-action-dropdown>
                    <x-report-button type="post" :id="$post->id" />
                    @can('update', $post)
                        <a href="{{ route('posts.edit', $post) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                            Edit Post
                        </a>
                    @endcan
                </x-action-dropdown>
            </div>

            {{-- Title & Body --}}
            <a href="{{ route('posts.show', $post) }}" class="block group">
                <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                    {{ request('q') ? Str::of($post->title)->highlight(request('q')) : $post->title }}
                </h2>

                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 leading-relaxed mb-4">
                    @php
                        $plainText = strip_tags($post->body);
                        $truncated = Str::limit($plainText, 160);
                    @endphp
                    {{ request('q') ? Str::of($truncated)->highlight(request('q')) : $truncated }}
                </p>
            </a>

            {{-- Footer Tags & Mobile Stats --}}
            <div class="flex items-center justify-between">
                <div class="flex flex-wrap gap-2">
    @foreach($post->tags->take(3) as $tag)
        {{-- Passing the whole tag object --}}
        <x-tag-badge :tag="$tag" />
    @endforeach
</div>

                <div class="flex md:hidden gap-4 text-xs font-bold text-gray-500">
                    <div class="flex items-center gap-1">
                        <x-icons.arrow-up class="w-3.5 h-3.5" />
                        <span>{{ $post->upvote_count - $post->downvote_count }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <x-icons.chat class="w-3.5 h-3.5" />
                        <span>{{ $post->allComments()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>