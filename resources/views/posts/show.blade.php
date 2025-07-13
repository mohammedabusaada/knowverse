@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
    <p class="text-sm text-gray-500">By {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}</p>

    <div class="mt-4">
        {!! nl2br(e($post->body)) !!}
    </div>

    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Add a Comment</h2>

        @auth
            <form action="{{ route('comments.store', $post) }}" method="POST" class="mb-6">
                @csrf
                <textarea name="body" class="w-full p-2 border rounded mb-2" rows="3" placeholder="Add a comment..."></textarea>
                <input type="hidden" name="parent_id" value="">
                <button type="submit" class="bg-blue-500 text-white px-4 py-1 rounded">Comment</button>
            </form>
        @else
            <p class="text-sm text-gray-600 mb-4">You must <a href="{{ route('login') }}" class="text-blue-600 underline">log
                    in</a> to comment.</p>
        @endauth
    </div>

    <div class="mt-6">
        <h2 class="text-xl font-semibold mb-2">Comments</h2>

        @foreach ($comments as $comment)
            @include('posts.partials.comment', ['comment' => $comment])
        @endforeach
    </div>
@endsection
