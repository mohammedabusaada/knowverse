@props(['post', 'comments'])

<div class="space-y-12">
    {{-- 1. Section Heading --}}
    <div class="flex items-baseline justify-between mb-8">
        <h2 class="font-heading text-2xl md:text-3xl font-bold text-ink">
            Discussion
            <sup class="font-mono text-[10px] text-muted font-normal ml-1">[{{ $post->comments_count ?? $comments->count() }}]</sup>
        </h2>
    </div>

    {{-- 2. "Write a Response" Form --}}
    @auth
        <div class="mb-12">
            <form action="{{ route('comments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                
                <div class="border border-rule bg-paper p-1 focus-within:border-accent focus-within:shadow-sm transition-all">
                    <textarea 
                        name="body" 
                        placeholder="Contribute your insights to the discussion..." 
                        rows="3" 
                        required 
                        class="w-full bg-transparent border-none focus:ring-0 resize-y text-ink font-serif text-[15px] placeholder:text-muted/50 p-4 leading-relaxed"
                    ></textarea>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="font-mono text-[10px] uppercase tracking-widest font-bold bg-ink text-paper px-8 py-2.5 border border-ink hover:bg-transparent hover:text-ink transition-colors shadow-sm focus:outline-none">
                        Publish Response
                    </button>
                </div>
            </form>
        </div>
    @else
        {{-- Guest Prompt --}}
        <div class="mb-12 py-10 border border-dashed border-rule text-center bg-aged/30">
            <p class="font-serif text-muted text-[15px] italic">
                Please <a href="{{ route('login') }}" class="text-ink font-bold border-b border-ink hover:text-accent hover:border-accent transition-colors">authenticate</a> to participate in the discussion.
            </p>
        </div>
    @endauth

    {{-- 3. The Comments Thread --}}
    <div class="space-y-0">
        @forelse ($comments as $comment)
            @if(is_null($comment->parent_id))
                <x-comment :comment="$comment" />
            @endif
        @empty
            <div class="py-20 text-center">
                <p class="font-serif text-muted italic text-lg">There are no responses yet. Be the first to contribute.</p>
            </div>
        @endforelse
    </div>
</div>