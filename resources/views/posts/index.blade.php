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
    <!-- Tags Filter -->
<div class="mb-10 bg-white dark:bg-gray-800 shadow p-6 rounded-xl border dark:border-gray-700">

    <h2 class="text-xl font-bold mb-4 dark:text-white">Filter by Tags</h2>

    <form method="GET" action="{{ route('posts.index') }}" class="space-y-4">

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">

            @foreach ($tags as $tag)
                <label class="flex items-center gap-2 p-2 rounded-lg cursor-pointer
                               border dark:border-gray-600
                               hover:bg-gray-100 dark:hover:bg-gray-700">

                    <input type="checkbox"
                           name="tags[]"
                           value="{{ $tag->name }}"
                           class="rounded text-blue-600 focus:ring-blue-500"
                           {{ in_array($tag->name, $selectedTags) ? 'checked' : '' }}>

                    <span class="text-gray-800 dark:text-gray-200">
                        {{ $tag->name }}
                    </span>
                </label>
            @endforeach

        </div>

        <div class="mt-4 flex gap-4">
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Apply Filters
            </button>

            <a href="{{ route('posts.index') }}"
               class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200
                      rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                Clear
            </a>
        </div>

    </form>
</div>




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
