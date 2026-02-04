@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="min-w-0">
            <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">
                All Discussions
            </h1>

            @if(!empty($selectedTags))
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Showing posts with specific tags
                </p>
            @endif
        </div>

        <div class="shrink-0">
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
    </div>

    {{-- Active Filters Banner (Monochrome + Blue accent) --}}
    @if(!empty($selectedTags))
        <div class="mb-6 rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-4">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                    Filtered by
                </span>

                @foreach($selectedTags as $tag)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full
                                 text-xs font-semibold
                                 bg-gray-100 text-gray-800 border border-gray-200
                                 dark:bg-gray-950 dark:text-gray-200 dark:border-gray-800">
                        <span class="text-blue-600 dark:text-blue-400">#</span>{{ $tag }}
                    </span>
                @endforeach

                <a href="{{ route('posts.index') }}"
                   class="ml-auto text-xs font-semibold
                          text-gray-500 hover:text-blue-600
                          dark:text-gray-400 dark:hover:text-blue-400 transition">
                    Clear all filters
                </a>
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if ($posts->isEmpty())
        <div class="text-center py-16 sm:py-20
                    bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-800
                    rounded-2xl">
            <p class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-gray-100">
                No discussions found
            </p>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Try adjusting your filters or start a new discussion.
            </p>

            <div class="mt-6">
                <a href="{{ route('posts.index') }}"
                   class="text-sm font-semibold text-blue-600 hover:text-blue-700
                          dark:text-blue-400 dark:hover:text-blue-300 transition">
                    View all posts
                </a>
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
                <div class="lg:hidden mb-6">
                    @include('posts.partials.filters')
                </div>

                {{-- Posts List --}}
                <div class="space-y-2">
                    @foreach ($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $posts->links() }}
                </div>

            </div>
        </div>
    @endif

</div>
@endsection

