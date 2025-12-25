@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

    {{-- Intro / Identity --}}
    <section class="text-center max-w-3xl mx-auto space-y-4">
        <h1 class="text-4xl font-bold dark:text-white">
            KnowVerse
        </h1>

        <p class="text-lg text-gray-600 dark:text-gray-300">
            A platform for structured scientific dialogue and academic knowledge exchange.
        </p>
    </section>

    {{-- Primary CTA --}}
<section class="text-center">
    @auth
        <a href="{{ route('posts.create') }}"
           class="inline-flex items-center px-6 py-3 rounded-lg
                  bg-blue-600 text-white font-semibold
                  hover:bg-blue-700 transition">
            Start a discussion
        </a>
    @else
        <a href="{{ route('login') }}"
           class="inline-flex items-center px-6 py-3 rounded-lg
                  bg-gray-800 text-white font-semibold
                  hover:bg-gray-900 transition">
            Sign in to start a discussion
        </a>
    @endauth
</section>


    {{-- Recent Discussions --}}
    <section class="space-y-6">

        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold dark:text-white">
                Recent Discussions
            </h2>

            <a href="{{ route('posts.index') }}"
               class="text-blue-600 hover:underline font-medium">
                Browse all →
            </a>
        </div>

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($recentPosts as $post)
                <x-post-card :post="$post" compact />
            @empty
                <div class="col-span-full text-center py-16 text-gray-500">
                    No discussions yet.
                </div>
            @endforelse
        </div>

    </section>

    {{-- Popular Topics --}}
    <section class="space-y-6">

        <h2 class="text-2xl font-semibold dark:text-white">
            Popular Topics
        </h2>

        <div class="flex flex-wrap gap-3">
            @foreach ($popularTags as $tag)
                <a href="{{ route('posts.index', ['tags[]' => $tag->name]) }}"
                   class="px-4 py-2 rounded-full text-sm font-medium
                          bg-gray-100 dark:bg-gray-700
                          text-gray-700 dark:text-gray-200
                          hover:bg-gray-200 dark:hover:bg-gray-600">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>

    </section>

</div>
@endsection
