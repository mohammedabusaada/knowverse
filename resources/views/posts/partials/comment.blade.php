<div
    style="margin-left: {{ $comment->parent_id ? '20px' : '0' }}; border-left: 1px solid #ccc; padding-left: 10px; margin-top: 10px;">
    <strong>{{ $comment->user->name }}</strong>
    <p>{{ $comment->body }}</p>

    @if ($comment->replies)
        @foreach ($comment->replies as $reply)
            @include('posts.partials.comment', ['comment' => $reply])
        @endforeach
    @endif
</div>
