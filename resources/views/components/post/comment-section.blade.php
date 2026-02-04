@props(['post', 'comments'])

<div class="space-y-8">
    {{-- 1. Comment Count & Heading --}}
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
            Discussion
            <span class="text-sm font-normal text-gray-500 bg-gray-100 dark:bg-gray-800 px-2 py-0.5 rounded-full">
                {{ $post->comments_count ?? $comments->count() }}
            </span>
        </h2>
    </div>

    {{-- 2. "Write a Comment" Form (Only for logged in users) --}}
    @auth
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm">
            <form action="{{ route('comments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                
                <x-textarea 
                    name="body" 
                    placeholder="What are your thoughts?" 
                    rows="3" 
                    required 
                    class="border-none focus:ring-0 bg-transparent resize-none"
                ></x-textarea>

                <div class="flex justify-end mt-2 pt-2 border-t border-gray-100 dark:border-gray-800">
                    <x-button primary class="px-6">
                        Post Comment
                    </x-button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-gray-50 dark:bg-gray-800/50 border border-dashed border-gray-300 dark:border-gray-700 rounded-2xl p-6 text-center">
            <p class="text-gray-600 dark:text-gray-400 text-sm">
                Please <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">log in</a> to join the conversation.
            </p>
        </div>
    @endauth

    {{-- 3. The Comments List --}}
    <div class="space-y-6">
        @forelse ($comments as $comment)
            {{-- We only show top-level comments here; children are handled recursively inside the component --}}
            @if(is_null($comment->parent_id))
                <x-comment :comment="$comment" />
            @endif
        @empty
            <div class="py-12 text-center">
                <div class="text-gray-400 mb-2">
                    <svg class="w-12 h-12 mx-auto opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.268 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-gray-500 dark:text-gray-400">No comments yet. Be the first to start the discussion!</p>
            </div>
        @endforelse
    </div>
</div>