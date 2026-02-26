@props(['comment'])

<div
    id="comment-{{ $comment->id }}"
    x-data="{ showReply: false, showEdit: false }"
    class="py-8 border-b border-rule last:border-0 transition-all scroll-mt-24"
>
    <div class="flex items-start gap-4 sm:gap-6">

        <div class="shrink-0 pt-1">
            <x-comment-vote :comment="$comment" />
        </div>

        <div class="shrink-0 hidden sm:block pt-1">
            <x-user-avatar :src="$comment->user->profile_picture_url" size="sm" class="grayscale opacity-80" />
        </div>

        <div class="flex-1 min-w-0">
            {{-- Comment Header --}}
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="min-w-0 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <span class="font-heading font-bold text-ink text-base">
                        {{ $comment->user->display_name }}
                    </span>

                    <span class="font-mono text-[10px] uppercase tracking-widest text-muted">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>

                    @if($comment->is_hidden)
                        <span class="font-mono text-[9px] uppercase tracking-widest text-[#a65a38] border border-[#a65a38]/30 px-1.5 py-0.5 ml-2">
                            Hidden
                        </span>
                    @endif

                    @if ($comment->post->best_comment_id === $comment->id)
                        <span class="font-mono text-[9px] uppercase tracking-[0.15em] text-paper bg-ink px-2 py-0.5 ml-2 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Best Answer
                        </span>
                    @endif
                </div>

                <x-action-dropdown>
                    @if(auth()->id() === $comment->post->user_id && is_null($comment->parent_id))
                        @if ($comment->post->best_comment_id === $comment->id)
                            <form method="POST" action="{{ route('comments.unbest', $comment) }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition">Remove Best Answer</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('comments.best', $comment) }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-1.5 text-sm font-serif font-bold text-ink hover:bg-aged transition">Mark as Best</button>
                            </form>
                        @endif
                        <div class="my-1 border-t border-rule"></div>
                    @endif

                    @can('update', $comment)
                        <button @click="showEdit = !showEdit" class="block w-full text-left px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition">Edit Response</button>
                    @endcan

                    @can('delete', $comment)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Delete this response?');">
                            @csrf @method('DELETE')
                            <button class="block w-full text-left px-4 py-1.5 text-sm font-serif text-accent-warm hover:bg-aged transition">Delete Response</button>
                        </form>
                    @endcan

                    <div class="my-1 border-t border-rule"></div>
                    <x-report-button type="comment" :id="$comment->id" />
                </x-action-dropdown>
            </div>

            @if($comment->is_hidden)
                <div class="mb-4 text-[13px] font-serif text-accent-warm italic border-l-2 border-accent-warm pl-3">
                    This response has been hidden from public view for violating community guidelines.
                </div>
            @endif

            {{-- Comment Body --}}
            <div x-show="!showEdit" x-transition.opacity class="prose dark:prose-invert max-w-none font-serif text-ink text-[15px] leading-relaxed mb-4">
                <x-markdown :text="$comment->body" />
            </div>

            {{-- Edit Form --}}
            @can('update', $comment)
                <div x-show="showEdit" x-cloak x-transition class="mb-4 border border-rule p-1">
                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                        @csrf @method('PUT')
                        <textarea name="body" rows="3" required class="w-full bg-transparent border-none focus:ring-0 text-ink font-serif text-sm p-3">{{ $comment->body }}</textarea>
                        <div class="flex gap-3 mt-2 justify-end p-2 border-t border-rule">
                            <button type="button" @click="showEdit = false" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink">Cancel</button>
                            <button type="submit" class="font-mono text-[10px] uppercase tracking-widest text-paper bg-ink px-4 py-1.5 hover:opacity-80">Save</button>
                        </div>
                    </form>
                </div>
            @endcan

            {{-- Reply Button --}}
            @auth
                <button @click="showReply = !showReply" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors pb-0.5 border-b border-transparent hover:border-ink">
                    Reply to this
                </button>
            @endauth

            {{-- Reply Form --}}
            <div x-show="showReply" x-cloak x-transition class="mt-4 border border-rule p-1 bg-aged/20">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="body" rows="2" placeholder="Draft your reply..." required class="w-full bg-transparent border-none focus:ring-0 text-ink font-serif text-sm p-3"></textarea>
                    <div class="flex gap-3 mt-2 justify-end p-2 border-t border-rule">
                        <button type="button" @click="showReply = false" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink">Cancel</button>
                        <button type="submit" class="font-mono text-[10px] uppercase tracking-widest text-paper bg-ink px-4 py-1.5 hover:opacity-80">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Nested Replies --}}
    @if ($comment->replies->count())
        <div class="mt-6 ml-8 sm:ml-12 border-l border-rule pl-4 sm:pl-8 space-y-0">
            @foreach ($comment->replies as $reply)
                <x-comment :comment="$reply" />
            @endforeach
        </div>
    @endif
</div>