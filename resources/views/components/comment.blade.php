@props(['comment'])

<div 
    class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 
           bg-white dark:bg-gray-800"
    x-data="{ showReply: false, showEdit: false }">

    <!-- Header -->
    <div class="flex items-start gap-3">

        <x-user-avatar :src="$comment->user->profile_picture_url" size="md" />

        <div class="flex-1">

            <!-- User + Date + Actions -->
            <div class="flex items-center justify-between">

                <div>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                        {{ $comment->user->display_name }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        · {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>

                <div class="flex gap-3 text-sm">

                    @can('update', $comment)
                        <button
                            @click="showEdit = !showEdit"
                            class="text-yellow-600 dark:text-yellow-400 hover:underline">
                            Edit
                        </button>
                    @endcan

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

            <!-- Body -->
            <div x-show="!showEdit" class="mt-3">
                <x-markdown :text="$comment->body" />
            </div>

            <!-- Edit Form -->
            @can('update', $comment)
                <div x-show="showEdit" class="mt-3">
                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <x-textarea name="body" rows="3">
                            {{ $comment->body }}
                        </x-textarea>

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

            <!-- Reply Toggle -->
            @auth
            <button @click="showReply = !showReply"
                    class="mt-3 text-blue-600 dark:text-blue-400 text-sm hover:underline">
                Reply
            </button>
            @endauth

            <!-- Reply Form -->
            <div x-show="showReply" class="mt-3">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <x-textarea name="body" rows="2" placeholder="Write a reply..."></x-textarea>

                    <x-button class="mt-2 bg-blue-600 hover:bg-blue-700">
                        Reply
                    </x-button>
                </form>
            </div>

        </div>

    </div>

    <!-- Replies -->
    @if ($comment->replies->count())
        <div class="ml-12 mt-6 space-y-6 border-l border-gray-300 dark:border-gray-700 pl-6">
            @foreach ($comment->replies as $reply)
                <x-comment :comment="$reply" />
            @endforeach
        </div>
    @endif

</div>
