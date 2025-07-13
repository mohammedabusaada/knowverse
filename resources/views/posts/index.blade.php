@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">All Posts</h1>

    @foreach ($posts as $post)
        <div class="mb-6 border-b pb-4">
            <h2 class="text-xl font-semibold">
                <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
            </h2>
            <p class="text-sm text-gray-600">By {{ $post->user->name }} • {{ $post->created_at->diffForHumans() }}</p>
            <p class="mt-2">{{ Str::limit($post->body, 150) }}</p>
        </div>
    @endforeach

    {{ $posts->links() }}
@endsection
