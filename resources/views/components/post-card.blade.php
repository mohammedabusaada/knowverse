@props(['post', 'compact' => false])

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:border-blue-400 dark:hover:border-blue-500 transition-colors shadow-sm overflow-visible mb-4">
    <div class="p-4 md:p-5 flex gap-4">
        
        {{-- Sidebar Stats --}}
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
            <div class="flex items-center gap-2 mb-2 text-xs text-gray-500">
                <x-user-avatar :src="$post->user->profile_picture_url" size="xs" />
                <x-user-hover-card :user="$post->user" />
                <span>•</span>
                <span>{{ $post->created_at->diffForHumans() }}</span>
            </div>

            <a href="{{ route('posts.show', $post) }}" class="block group">
                <h2 class="text-xl font-bold mb-2 text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                    {{-- Highlight Title if search query exists --}}
                    @if(request('q'))
                        @highlight($post->title)
                    @else
                        {{ $post->title }}
                    @endif
                </h2>

                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-2 leading-relaxed mb-4">
                    {{-- Highlight Body snippet if search query exists --}}
                    @php 
                        $plainText = strip_tags($post->body);
                        $truncated = Str::limit($plainText, 160);
                    @endphp

                    @if(request('q'))
                        @highlight($truncated)
                    @else
                        {{ $truncated }}
                    @endif
                </p>
            </a>

            <div class="flex items-center justify-between">
                <div class="flex flex-wrap gap-2">
                    @foreach($post->tags->take(3) as $tag)
                        {{-- FIXED LINK: Sends an array with one tag to match the Controller logic --}}
                        <a href="{{ route('posts.index', ['tags' => [$tag->name]]) }}" 
                           class="text-xs text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 px-2 py-0.5 rounded hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>

                <div class="flex md:hidden gap-4 text-xs font-bold text-gray-500">
                    <span>▲ {{ $post->upvote_count - $post->downvote_count }}</span>
                    <span>💬 {{ $post->allComments()->count() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>