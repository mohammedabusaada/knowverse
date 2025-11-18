@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Header -->
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-3xl font-bold dark:text-white">Latest Posts</h1>

        <x-button tag="a" href="{{ route('posts.create') }}" primary>
            + Create Post
        </x-button>
    </div>

    <!-- Empty State -->
    @if ($posts->isEmpty())

    <div class="text-center py-24 text-gray-600 dark:text-gray-300">
        <p class="text-xl font-semibold">No posts available yet.</p>
        <p class="mt-2">Be the first to create one!</p>
    </div>

    @else

    <!-- Posts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach ($posts as $post)
        <x-post-card :post="$post" />
        @endforeach

    </div>

    <!-- Pagination -->
    <div class="mt-10">
        {{ $posts->links() }}
    </div>

    @endif

</div>

@endsection