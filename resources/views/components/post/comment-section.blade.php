@props(['post', 'comments'])

<div class="space-y-12">
    {{-- 1. Heading --}}
    <div class="flex items-baseline justify-between mb-8">
        <h2 class="font-heading text-2xl md:text-3xl font-bold text-ink">
            Discourse
            <sup class="font-mono text-xs text-muted font-normal ml-1">[{{ $post->comments_count ?? $comments->count() }}]</sup>
        </h2>
    </div>

    {{-- 2. "Write a Comment" Form --}}
    @auth
        <div class="mb-12">
            <form action="{{ route('comments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                
                <div class="border border-rule bg-transparent p-1 focus-within:border-ink transition-colors">
                    <textarea 
                        name="body" 
                        placeholder="Contribute to the discussion..." 
                        rows="3" 
                        required 
                        class="w-full bg-transparent border-none focus:ring-0 resize-y text-ink font-serif text-base placeholder:text-muted/50 p-3"
                    ></textarea>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="font-serif text-sm font-bold bg-ink text-paper px-8 py-2 border border-ink hover:bg-transparent hover:text-ink transition-colors">
                        Publish Response
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="mb-12 py-10 border border-dashed border-rule text-center bg-aged/30">
            <p class="font-serif text-muted text-[15px] italic">
                Please <a href="{{ route('login') }}" class="text-ink font-bold border-b border-ink hover:text-accent hover:border-accent transition-colors">sign in</a> to join the discourse.
            </p>
        </div>
    @endauth

    {{-- 3. The Comments List --}}
    <div class="space-y-0">
        @forelse ($comments as $comment)
            @if(is_null($comment->parent_id))
                <x-comment :comment="$comment" />
            @endif
        @empty
            <div class="py-20 text-center">
                <p class="font-serif text-muted italic text-lg">No responses have been recorded yet.</p>
            </div>
        @endforelse
    </div>
</div>