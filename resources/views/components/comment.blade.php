@props(['comment'])

<div
    id="comment-{{ $comment->id }}"
    x-data="{ showReply: false, showEdit: false }"
    class="rounded-xl border border-gray-200 dark:border-gray-700
           bg-white dark:bg-gray-800 p-5 shadow-sm transition
           scroll-mt-24">




    <!-- HEADER -->
    <div class="flex items-start gap-4">

        <!-- Voting (NEW) -->
        <x-comment-vote :comment="$comment" />

        <!-- Avatar -->
        <x-user-avatar :src="$comment->user->profile_picture_url" size="10" />

        <div class="flex-1">

            <!-- User row -->
            <div class="flex items-center justify-between">

                <div class="flex flex-col">
                    <div class="flex items-center gap-2">

                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ $comment->user->display_name }}
                        </span>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>

                        <!-- Best Comment Badge -->
                        @if ($comment->post->best_comment_id === $comment->id)
                        <span class="px-2 py-0.5 text-[11px] font-semibold rounded-md 
                                         bg-green-200 text-green-800 dark:bg-green-700 dark:text-white">
                            ✓ Best Answer
                        </span>
                        @endif

                    </div>
                </div>

                <!-- ACTION BUTTONS -->
                <div class="flex items-center gap-3 text-sm">

                    {{-- MARK AS BEST (Post owner only) --}}
                    @if(auth()->id() === $comment->post->user_id && is_null($comment->parent_id))

                    @if ($comment->post->best_comment_id === $comment->id)
                    <form method="POST" action="{{ route('comments.unbest', $comment) }}">
                        @csrf
                        <button class="text-red-600 dark:text-red-400 hover:underline font-medium">
                            Remove
                        </button>
                    </form>
                    @else
                    <form method="POST" action="{{ route('comments.best', $comment) }}">
                        @csrf
                        <button class="text-green-600 dark:text-green-400 hover:underline font-medium">
                            Mark
                        </button>
                    </form>
                    @endif

                    @endif

                    {{-- EDIT --}}
                    @can('update', $comment)
                    <button
                        @click="showEdit = !showEdit"
                        class="text-yellow-600 dark:text-yellow-400 hover:underline font-medium">
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

                        <button class="text-red-600 dark:text-red-400 hover:underline font-medium">
                            Delete
                        </button>
                    </form>
                    @endcan

                </div>

            </div>

            <!-- COMMENT BODY -->
            <div
                x-show="!showEdit"
                x-transition.opacity
                class="mt-3 prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                <x-markdown :text="$comment->body" />
            </div>

            <!-- EDIT FORM -->
            @can('update', $comment)
            <div
                x-show="showEdit"
                x-transition
                class="mt-4 bg-gray-50 dark:bg-gray-700/40 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                <form action="{{ route('comments.update', $comment) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <x-textarea name="body" rows="3" required>{{ $comment->body }}</x-textarea>

                    <div class="flex gap-2 mt-3">
                        <x-button class="bg-green-600 hover:bg-green-700 text-white">Save</x-button>

                        <x-button type="button"
                            @click="showEdit = false"
                            class="bg-gray-300 dark:bg-gray-600 text-black dark:text-white">
                            Cancel
                        </x-button>
                    </div>
                </form>
            </div>
            @endcan

            <!-- REPLY BUTTON -->
            @auth
            <button
                @click="showReply = !showReply"
                class="mt-4 text-blue-600 dark:text-blue-400 text-sm font-medium hover:underline">
                Reply
            </button>
            @endauth

            <!-- REPLY FORM -->
            <div
                x-show="showReply"
                x-transition
                class="mt-3 bg-gray-50 dark:bg-gray-700/40 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <x-textarea name="body" rows="2" placeholder="Reply…" required></x-textarea>

                    <div class="flex gap-2 mt-3">
                        <x-button class="bg-blue-600 hover:bg-blue-700 text-white">Reply</x-button>

                        <button
                            type="button"
                            @click="showReply = false"
                            class="text-gray-600 dark:text-gray-300 text-sm hover:underline">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- REPLIES -->
    @if ($comment->replies->count())
    <div
        class="mt-6 ml-12 pl-6 border-l border-gray-300 dark:border-gray-700 space-y-6">
        @foreach ($comment->replies as $reply)
        <x-comment :comment="$reply" />
        @endforeach
    </div>
    @endif

</div>