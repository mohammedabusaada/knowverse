@extends('layouts.app')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-10">
        <div>
            <h1 class="text-3xl font-bold dark:text-white">All Discussions</h1>
            @if(!empty($selectedTags))
                <p class="text-sm text-gray-500 mt-1">Showing posts with specific tags</p>
            @endif
        </div>

        @auth
            <x-button tag="a" href="{{ route('posts.create') }}" primary>
                + Start a Discussion
            </x-button>
        @else
            <x-button tag="a" href="{{ route('login') }}" secondary>
                Sign in to contribute
            </x-button>
        @endauth
    </div>

    {{-- Active Filters Banner --}}
    @if(!empty($selectedTags))
        <div class="mb-8 flex flex-wrap items-center gap-3 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-2xl">
            <span class="text-sm font-bold text-blue-700 dark:text-blue-400">Filtered by:</span>
            @foreach($selectedTags as $tag)
                <span class="px-3 py-1 bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-700 text-blue-600 dark:text-blue-400 text-xs font-bold rounded-full">
                    #{{ $tag }}
                </span>
            @endforeach
            <a href="{{ route('posts.index') }}" class="ml-auto text-xs font-bold text-gray-500 hover:text-red-500 transition">
                Clear all filters
            </a>
        </div>
    @endif

    {{-- Empty State --}}
    @if ($posts->isEmpty())
        <div class="text-center py-24 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700">
            <p class="text-xl font-semibold">No discussions found.</p>
            <p class="mt-2 text-gray-500">Try adjusting your filters or start a new discussion.</p>
            <div class="mt-6">
                 <a href="{{ route('posts.index') }}" class="text-blue-600 font-bold hover:underline">View all posts</a>
            </div>
        </div>
    @else

    {{-- Content Grid --}}
    <div class="lg:grid lg:grid-cols-4 lg:gap-8">

        {{-- Desktop Sidebar --}}
        <aside class="hidden lg:block lg:col-span-1">
            <div class="sticky top-24">
                @include('posts.partials.filters')
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="lg:col-span-3">

            {{-- Mobile Filters --}}
            <div class="lg:hidden mb-8">
                @include('posts.partials.filters')
            </div>

            {{-- Posts Grid --}}
            <div class="flex flex-col gap-1">
                @foreach ($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection