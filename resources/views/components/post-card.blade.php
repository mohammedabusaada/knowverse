@props(['post'])

<a href="{{ route('posts.show', $post) }}"
   class="block rounded-xl overflow-hidden shadow hover:shadow-lg transition 
          transform hover:-translate-y-1 bg-white dark:bg-gray-800
          border border-gray-200 dark:border-gray-700">

    <div class="p-6">

        <!-- Title -->
        <h2 class="text-xl font-semibold mb-2 dark:text-white line-clamp-1">
            {{ $post->title }}
        </h2>

        <!-- Author -->
        <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-4">
            <x-user-avatar :src="$post->user->profile_picture_url" size="sm" class="mr-3" />
            <div>
                <p class="font-medium">{{ $post->user->display_name }}</p>
                <p class="text-xs">{{ $post->created_at->diffForHumans() }}</p>
            </div>
        </div>

        <!-- Body Preview -->
        <p class="text-gray-700 dark:text-gray-300 mb-4 line-clamp-3">
            {{ Str::limit(strip_tags($post->body), 150) }}
        </p>

        <!-- Tags -->
        @if ($post->tags->count())
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($post->tags as $tag)
                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 
                                 text-gray-700 dark:text-gray-300
                                 text-xs rounded-md">
                        #{{ $tag->name }}
                    </span>
                @endforeach
            </div>
        @endif

        <x-post-stats 
            :views="$post->view_count"
            :comments="$post->comments_count ?? $post->comments->count()"
            :votes="$post->upvote_count - $post->downvote_count"
        />

    </div>

</a>
