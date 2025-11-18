@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">

    <!-- Back Link -->
    <div class="mb-6">
        <a href="{{ route('posts.index') }}"
            class="text-blue-600 hover:underline dark:text-blue-400">
            ← Back to Posts
        </a>
    </div>

    <!-- Post Container -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                rounded-xl shadow p-6">

        <!-- Title -->
        <h1 class="text-3xl font-bold mb-4 dark:text-white">
            {{ $post->title }}
        </h1>

        <!-- Author -->
        <x-post-author :user="$post->user" :date="$post->created_at" />

        <!-- Image -->
        @if ($post->image)
        <img src="{{ asset('storage/'.$post->image) }}"
            class="rounded-lg mb-6 shadow max-h-96 object-cover w-full">
        @endif

        <!-- Body -->
        <div class="prose dark:prose-invert max-w-none mb-6">
            <x-markdown :text="$post->body" />
        </div>

        <!-- Tags -->
        @if ($post->tags->count())
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($post->tags as $tag)
            <x-tag-badge :tag="$tag->name" />
            @endforeach
        </div>
        @endif

        <!-- Stats (views, comments, votes) -->
        <x-post-stats
            :views="$post->view_count"
            :comments="$post->comments()->count()"
            :votes="$post->upvote_count - $post->downvote_count" />

        <!-- Edit/Delete Actions -->
        @can('update', $post)
        <div class="mt-6 flex gap-4">

            <x-button tag="a"
                href="{{ route('posts.edit', $post) }}"
                class="bg-yellow-500 hover:bg-yellow-600">
                Edit Post
            </x-button>

            <form action="{{ route('posts.destroy', $post) }}"
                method="POST"
                onsubmit="return confirm('Are you sure you want to delete this post?');">
                @csrf
                @method('DELETE')

                <x-button class="bg-red-600 hover:bg-red-700">Delete</x-button>
            </form>

        </div>
        @endcan

    </div>

    <!-- Comments Section -->
    <div class="mt-10 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                rounded-xl shadow p-6">

        <h2 class="text-2xl font-semibold mb-6 dark:text-white">
            Comments ({{ $post->comments()->count() }})
        </h2>

        <!-- Add Comment -->
        @auth
        <form action="{{ route('comments.store') }}" method="POST" class="mb-8">
            @csrf

            <input type="hidden" name="post_id" value="{{ $post->id }}">

            <x-textarea
                name="body"
                placeholder="Write a comment..."
                rows="3"
                required></x-textarea>

            <x-button primary class="mt-3">Post Comment</x-button>
        </form>
        @endauth

        @guest
        <p class="text-gray-600 dark:text-gray-300 mb-6">
            <a href="{{ route('login') }}"
                class="text-blue-600 dark:text-blue-400 underline">Login</a>
            to post a comment.
        </p>
        @endguest

        <!-- Comments List -->
        <div class="space-y-6">
            @foreach ($post->comments()->whereNull('parent_id')->get() as $comment)
            <x-comment :comment="$comment" />
            @endforeach
        </div>

    </div>

</div>

@endsection