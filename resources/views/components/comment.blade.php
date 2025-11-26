@props(['comment'])

<div 
    class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 
           bg-white dark:bg-gray-800"
    x-data="{ showReply: false, showEdit: false }">

    <div class="flex items-start gap-3">

        <!-- Avatar -->
        <x-user-avatar :src="$comment->user->profile_picture_url" size="10" />

        <div class="flex-1">

            <!-- Header -->
            <div class="flex items-center justify-between">

                <!-- Author + Time + Best Comment Badge -->
                <div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $comment->user->display_name }}
                    </span>

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        · {{ $comment->created_at->diffForHumans() }}
                    </span>

                    <!-- BEST COMMENT BADGE -->
                    @if ($comment->post->best_comment_id === $comment->id)
                        <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded 
                                     bg-green-200 text-green-800 dark:bg-green-700 dark:text-white">
                            ✓ Best Comment
                        </span>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3 text-sm">

                    {{-- MARK AS BEST / REMOVE BEST --}}
                    @if(auth()->id() === $comment->post->user_id && is_null($comment->parent_id))

                        @if ($comment->post->best_comment_id === $comment->id)
                            <!-- Remove Best -->
                            <form method="POST" action="{{ route('comments.unbest', $comment) }}">
                                @csrf
                                <button class="text-red-600 dark:text-red-400 hover:underline">
                                    Remove Best
                                </button>
                            </form>
                        @else
                            <!-- Mark as Best -->
                            <form method="POST" action="{{ route('comments.best', $comment) }}">
                                @csrf
                                <button class="text-green-600 dark:text-green-400 hover:underline">
                                    Mark as Best
                                </button>
                            </form>
                        @endif

                    @endif

                    {{-- EDIT --}}
                    @can('update', $comment)
                        <button
                            @click="showEdit = !showEdit"
                            class="text-yellow-600 dark:text-yellow-400 hover:underline">
                            Edit
                        </button>
                    @endcan

                    {{-- DELETE --}}
                    @can('delete', $comment)
                        <form action="{{ route('comments.destroy', $comment) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this comment?');">
                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 dark:text-red-400 hover:underline">
                                Delete
                            </button>
                        </form>
                    @endcan

                </div>
            </div>

            <!-- COMMENT BODY -->
            <div x-show="!showEdit" class="mt-3">
                <x-markdown :text="$comment->body" />
            </div>

            <!-- EDIT FORM -->
            @can('update', $comment)
                <div x-show="showEdit" class="mt-3">
                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <x-textarea name="body" rows="3" required>{{ $comment->body }}</x-textarea>

                        <div class="flex gap-2 mt-3">
                            <x-button class="bg-green-600 hover:bg-green-700">Save</x-button>

                            <x-button type="button"
                                      @click="showEdit = false"
                                      class="bg-gray-300 dark:bg-gray-700 text-black dark:text-white">
                                Cancel
                            </x-button>
                        </div>
                    </form>
                </div>
            @endcan

            <!-- REPLY BUTTON -->
            @auth
            <button @click="showReply = !showReply"
                    class="mt-3 text-blue-600 dark:text-blue-400 text-sm hover:underline">
                Reply
            </button>
            @endauth

            <!-- REPLY FORM -->
            <div x-show="showReply" class="mt-3">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <x-textarea name="body" rows="2" placeholder="Write a reply..." required></x-textarea>

                    <x-button class="mt-2 bg-blue-600 hover:bg-blue-700">
                        Reply
                    </x-button>
                </form>
            </div>

        </div>
    </div>

    <!-- REPLIES -->
    @if ($comment->replies->count())
        <div class="ml-12 mt-6 space-y-6 border-l border-gray-300 dark:border-gray-700 pl-6">
            @foreach ($comment->replies as $reply)
                <x-comment :comment="$reply" />
            @endforeach
        </div>
    @endif

</div>
