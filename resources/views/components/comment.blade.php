@props(['comment'])

<div
    id="comment-{{ $comment->id }}"
    x-data="{ showReply: false, showEdit: false }"
    class="rounded-2xl border border-gray-200 dark:border-gray-800
           bg-white dark:bg-gray-900 p-5 shadow-sm transition
           scroll-mt-24"
>
    <div class="flex items-start gap-4">

        <x-comment-vote :comment="$comment" />

        <x-user-avatar :src="$comment->user->profile_picture_url" size="10" />

        <div class="flex-1 min-w-0">

            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="font-semibold text-gray-900 dark:text-gray-100 truncate">
                            {{ $comment->user->display_name }}
                        </span>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>

                        @if ($comment->post->best_comment_id === $comment->id)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[11px] font-semibold rounded-md
                                         bg-gray-100 text-gray-800 border border-blue-600/20
                                         dark:bg-gray-950 dark:text-gray-200 dark:border-blue-500/25">
                                <span class="text-blue-600 dark:text-blue-400">✓</span>
                                Best Answer
                            </span>
                        @endif
                    </div>
                </div>

                <x-action-dropdown>
                    {{-- MARK AS BEST (Post owner only) --}}
                    @if(auth()->id() === $comment->post->user_id && is_null($comment->parent_id))
                        @if ($comment->post->best_comment_id === $comment->id)
                            <form method="POST" action="{{ route('comments.unbest', $comment) }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    Remove Best Answer
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('comments.best', $comment) }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100 dark:hover:bg-gray-700 font-medium transition">
                                    Mark as Best
                                </button>
                            </form>
                        @endif
                    @endif

                    {{-- EDIT --}}
                    @can('update', $comment)
                        <button @click="showEdit = !showEdit"
                                class="block w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 transition">
                            Edit Comment
                        </button>
                    @endcan

                    {{-- DELETE --}}
                    @can('delete', $comment)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?');">
                            @csrf @method('DELETE')
                            <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                Delete Comment
                            </button>
                        </form>
                    @endcan

                    {{-- REPORT --}}
                    <x-report-button type="comment" :id="$comment->id" />
                </x-action-dropdown>
            </div>

            <div x-show="!showEdit" x-transition.opacity class="mt-3 prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                <x-markdown :text="$comment->body" />
            </div>

            @can('update', $comment)
                <div x-show="showEdit" x-cloak x-transition class="mt-4 bg-gray-50 dark:bg-gray-950/40 p-4 rounded-xl border border-gray-200 dark:border-gray-800">
                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                        @csrf @method('PUT')
                        <x-textarea name="body" rows="3" required>{{ $comment->body }}</x-textarea>
                        <div class="flex gap-2 mt-3">
                            <x-button primary>Save</x-button>
                            <x-button type="button" secondary @click="showEdit = false">Cancel</x-button>
                        </div>
                    </form>
                </div>
            @endcan

            @auth
                <button @click="showReply = !showReply" class="mt-4 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition">
                    Reply
                </button>
            @endauth

            <div x-show="showReply" x-cloak x-transition class="mt-3 bg-gray-50 dark:bg-gray-950/40 p-4 rounded-xl border border-gray-200 dark:border-gray-800">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <x-textarea name="body" rows="2" placeholder="Write a reply…" required></x-textarea>
                    <div class="flex gap-2 mt-3">
                        <x-button primary>Reply</x-button>
                        <button type="button" @click="showReply = false" class="text-sm font-medium text-gray-600 hover:text-blue-600 dark:text-gray-300 dark:hover:text-blue-400 transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if ($comment->replies->count())
        <div class="mt-6 ml-4 md:ml-12 pl-4 md:pl-6 border-l border-gray-200 dark:border-gray-800 space-y-6">
            @foreach ($comment->replies as $reply)
                <x-comment :comment="$reply" />
            @endforeach
        </div>
    @endif
</div>