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
                rounded-2xl shadow-sm p-8">

        <!-- Title -->
        <h1 class="text-4xl font-bold mb-4 text-gray-900 dark:text-white leading-tight">
            {{ $post->title }}
        </h1>

        <!-- Author + Meta Info -->
        <div class="flex items-center justify-between mb-6 text-sm text-gray-600 dark:text-gray-400">

            <x-post-author :user="$post->user" :date="$post->created_at" />

            <div class="flex items-center gap-8">

                <!-- VOTING -->
                <x-post-vote :post="$post" />

                <!-- Views -->
                <div class="flex items-center gap-1">
                    <x-icons.eye class="w-4 h-4" />
                    {{ number_format($post->view_count) }}
                </div>

                <!-- Comments Count -->
                <div class="flex items-center gap-1">
                    <x-icons.chat class="w-4 h-4" />
                    {{ $post->allComments()->count() }}
                </div>

            </div>
        </div>

        <!-- Image -->
        @if ($post->image)
            <div class="mb-8">
                <img src="{{ asset('storage/'.$post->image) }}"
                     class="rounded-xl shadow max-h-[450px] w-full object-cover">
            </div>
        @endif

        <!-- Body -->
        <div class="prose dark:prose-invert max-w-none text-lg leading-relaxed mb-8">
            <x-markdown :text="$post->body" />
        </div>

        <!-- Tags -->
        @if ($post->tags->count())
            <div class="flex flex-wrap gap-2 mb-8">
                @foreach($post->tags as $tag)
                    <x-tag-badge :label="$tag->name" />
                @endforeach
            </div>
        @endif

        <!-- Post Actions (Edit/Delete) -->
        @can('update', $post)
        <div class="mt-8 flex gap-3 pt-6 border-t border-gray-300 dark:border-gray-700">

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

                <x-button class="bg-red-600 hover:bg-red-700">
                    Delete
                </x-button>
            </form>

        </div>
        @endcan

    </div>

    <!-- Comments Section -->
    <div class="mt-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                rounded-2xl shadow-sm p-8">

        <!-- Heading -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold dark:text-white">Comments</h2>

            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ $post->allComments()->count() }} total
            </span>
        </div>

        <!-- Add Comment -->
        @auth
        <div class="mb-9 p-4 rounded-xl border border-gray-300 dark:border-gray-700 
                    bg-gray-50 dark:bg-gray-700/30">

            <form action="{{ route('comments.store') }}" method="POST">
                @csrf

                <input type="hidden" name="post_id" value="{{ $post->id }}">

                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Add a Comment
                </label>

                <x-textarea
                    name="body"
                    rows="3"
                    placeholder="Write something..."
                    required></x-textarea>

                <div class="flex justify-end mt-3">
                    <x-button class="bg-blue-600 hover:bg-blue-700">
                        Post Comment
                    </x-button>
                </div>
            </form>

        </div>
        @endauth

        @guest
            <p class="text-gray-600 dark:text-gray-300 mb-6">
                <a href="{{ route('login') }}"
                   class="text-blue-600 dark:text-blue-400 underline">Login</a>
                to post a comment.
            </p>
        @endguest

        <!-- Comments List -->
        <div class="space-y-8">
            @foreach ($comments as $comment)
                <x-comment :comment="$comment" />
            @endforeach
        </div>

    </div>

</div>

@endsection
