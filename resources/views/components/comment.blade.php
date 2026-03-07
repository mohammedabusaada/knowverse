@props(['comment'])

<div
    id="comment-{{ $comment->id }}"
    x-data="{ showReply: false, showEdit: false }"
    class="py-8 border-b border-rule last:border-0 transition-all scroll-mt-24 group/comment"
>
    <div class="flex items-start gap-4 sm:gap-6">

        {{-- Voting Actions --}}
        <div class="shrink-0 pt-1">
            <x-comment-vote :comment="$comment" />
        </div>

        {{-- Scholar Avatar --}}
        <div class="shrink-0 hidden sm:block pt-1">
            <x-user-avatar :user="$comment->user" size="sm" class="grayscale opacity-80 group-hover/comment:grayscale-0 transition-all" />
        </div>

        <div class="flex-1 min-w-0">
            
            {{-- Response Header --}}
            <div class="flex items-start justify-between gap-3 mb-3">
                <div class="min-w-0 flex flex-wrap items-baseline gap-x-3 gap-y-1">
                    <span class="font-heading font-bold text-ink text-base">
                        {{ $comment->user->display_name }}
                    </span>

                    {{-- Original Author Badge --}}
                    @if($comment->user_id === $comment->post->user_id)
                        <span class="font-mono text-[8px] bg-ink text-paper px-1.5 py-0.5 rounded-sm uppercase tracking-widest">
                            Author
                        </span>
                    @endif

                    <span class="font-mono text-[10px] uppercase tracking-widest text-muted">
                        {{ $comment->created_at->diffForHumans() }}
                    </span>

                    {{-- Visibility Flag --}}
                    @if($comment->is_hidden)
                        <span class="font-mono text-[9px] uppercase tracking-widest text-accent-warm border border-accent-warm/30 px-1.5 py-0.5 ml-2">
                            Hidden
                        </span>
                    @endif

                    {{-- Author's Pick Badge --}}
                    @if ($comment->post->best_comment_id === $comment->id)
                        <span class="font-mono text-[9px] uppercase tracking-[0.15em] text-paper bg-accent border border-accent px-2 py-0.5 ml-2 flex items-center gap-1 shadow-sm">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="square" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Author's Pick
                        </span>
                    @endif
                </div>

                {{-- Actions Dropdown --}}
                <x-action-dropdown>
                    
                    {{-- Post Owner Actions: Highlight as Author's Pick --}}
                    @if(auth()->id() === $comment->post->user_id && is_null($comment->parent_id))
                        @if ($comment->post->best_comment_id === $comment->id)
                            <form method="POST" action="{{ route('comments.unbest', $comment) }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition-colors">Retract Pick</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('comments.best', $comment) }}">
                                @csrf
                                <button class="block w-full text-left px-4 py-1.5 text-sm font-serif font-bold text-accent hover:bg-accent/10 transition-colors">Highlight Response</button>
                            </form>
                        @endif
                        <div class="my-1 border-t border-rule"></div>
                    @endif

                    @can('update', $comment)
                        <button @click="showEdit = !showEdit" class="block w-full text-left px-4 py-1.5 text-sm font-serif text-ink hover:bg-aged transition-colors">Edit Response</button>
                    @endcan

                    @can('delete', $comment)
                        <form action="{{ route('comments.destroy', $comment) }}" method="POST" onsubmit="return confirm('Permanently delete this response?');">
                            @csrf @method('DELETE')
                            <button class="block w-full text-left px-4 py-1.5 text-sm font-serif text-accent-warm hover:bg-accent-warm/10 transition-colors">Delete</button>
                        </form>
                    @endcan

                    <div class="my-1 border-t border-rule"></div>
                    <x-report-button type="comment" :id="$comment->id" />
                </x-action-dropdown>
            </div>

            {{-- Hidden Warning --}}
            @if($comment->is_hidden)
                <div class="mb-4 text-[13px] font-serif text-accent-warm italic border-l-2 border-accent-warm pl-3 bg-accent-warm/5 py-2">
                    This response is concealed from public indexing due to guideline violations.
                </div>
            @endif

            {{-- Rendered Response Body --}}
            <div x-show="!showEdit" x-transition.opacity class="prose dark:prose-invert max-w-none font-serif text-ink text-[15px] leading-relaxed mb-4 break-words">
                {!! \Illuminate\Support\Str::markdown($comment->body, ['html_input' => 'escape']) !!}
            </div>

            {{-- Inline Edit Form --}}
            @can('update', $comment)
                <div x-show="showEdit" x-cloak x-transition class="mb-4 border border-rule bg-paper p-1 shadow-sm focus-within:border-accent transition-colors">
                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                        @csrf @method('PUT')
                        <textarea name="body" rows="3" required class="w-full bg-transparent border-none focus:ring-0 text-ink font-serif text-[15px] p-3 resize-y">{{ $comment->body }}</textarea>
                        
                        <div class="flex gap-3 mt-2 justify-end p-2 border-t border-rule bg-aged/10">
                            <button type="button" @click="showEdit = false" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors focus:outline-none">Cancel</button>
                            <button type="submit" class="font-mono text-[10px] uppercase tracking-widest text-paper bg-accent px-5 py-2 hover:opacity-80 transition-opacity focus:outline-none">Update</button>
                        </div>
                    </form>
                </div>
            @endcan

            {{-- Reply Toggle Button --}}
            @auth
                <button @click="showReply = !showReply" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-accent transition-colors pb-0.5 border-b border-transparent hover:border-accent focus:outline-none">
                    Reply
                </button>
            @endauth

            {{-- Inline Reply Form --}}
            <div x-show="showReply" x-cloak x-transition class="mt-4 border border-rule bg-aged/30 p-1 focus-within:border-accent focus-within:bg-paper transition-colors">
                <form action="{{ route('comments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $comment->post_id }}">
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="body" rows="2" placeholder="Draft your reply..." required class="w-full bg-transparent border-none focus:ring-0 text-ink font-serif text-[15px] p-3 resize-y"></textarea>
                    
                    <div class="flex gap-3 mt-2 justify-end p-2 border-t border-rule">
                        <button type="button" @click="showReply = false" class="font-mono text-[10px] uppercase tracking-widest text-muted hover:text-ink transition-colors focus:outline-none">Cancel</button>
                        <button type="submit" class="font-mono text-[10px] uppercase tracking-widest text-paper bg-ink px-5 py-2 hover:opacity-80 transition-opacity focus:outline-none">Append Note</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Recursive Rendering of Nested Replies --}}
    @if ($comment->replies->isNotEmpty())
        <div class="mt-6 ml-8 sm:ml-12 border-l border-rule pl-4 sm:pl-8 space-y-0 relative before:content-[''] before:absolute before:left-[-1px] before:top-0 before:h-8 before:w-px before:bg-ink/20">
            @foreach ($comment->replies as $reply)
                <x-comment :comment="$reply" />
            @endforeach
        </div>
    @endif
</div>