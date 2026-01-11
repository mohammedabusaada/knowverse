@extends('layouts.app')

@section('content')
<div
    class="
        min-h-screen
        bg-gradient-to-br from-gray-100 to-gray-200
        dark:from-gray-900 dark:to-gray-800
        flex items-center justify-center
        py-16 px-4
    "
>
    <div class="w-full max-w-7xl space-y-20">

        {{-- Intro / Identity --}}
        <section class="text-center max-w-3xl mx-auto space-y-5">
            <h1
                class="
                    text-4xl sm:text-5xl
                    font-bold
                    text-gray-900 dark:text-white

                "
            >
                KnowVerse
            </h1>

            <p
                class="
                 px-4 py-2
                  font-medium
                   text-gray-700 dark:text-gray-200

                    text-lg

                    leading-relaxed
                "
            >
                A platform for structured scientific dialogue and academic knowledge exchange.
            </p>
        </section>

        {{-- Primary CTA --}}
        <section class="text-center">
            @auth
                <a
                    href="{{ route('posts.create') }}"
                    class="
                        px-4 py-2 rounded-lg
                              bg-black text-white
                            hover:bg-gradient-to-r
                            hover:from-gray-800 hover:to-gray-600
                            hover:text-white
                            hover:shadow-md
                            hover:scale-105
                            transition-all duration-200
                    "
                >
                    Start a discussion
                </a>
            @else
                <a
                    href="{{ route('login') }}"
                    class="px-4 py-2 rounded-lg
                              bg-black text-white
                            hover:bg-gradient-to-r
                            hover:from-gray-800 hover:to-gray-600
                            hover:text-white
                            hover:shadow-md
                            hover:scale-105
                            transition-all duration-200
                    "
                >
                    Sign in to start a discussion
                </a>
            @endauth
        </section>

        {{-- Popular Topics --}}
        <section class="mt-6 space-y-4">
            <h2
                class="
                    text-lg
                    font-semibold
                    text-gray-800 dark:text-gray-200
                "
            >
                Popular Topics
            </h2>

            <div class="flex flex-wrap gap-3">
                @foreach ($popularTags as $tag)
                    <a
                        href="{{ route('posts.index', ['tags[]' => $tag->name]) }}"
                        class="
                            px-4 py-2
                            rounded-full
                            text-sm font-medium
                            bg-white dark:bg-gray-800
                            text-gray-700 dark:text-gray-200
                            border border-gray-200 dark:border-gray-700
                            shadow-sm
                            hover:bg-gradient-to-r
                            hover:from-gray-800 hover:to-gray-600
                            hover:text-white
                            hover:shadow-md
                            hover:scale-105
                            transition-all duration-200
                        "
                    >
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- Recent Discussions --}}
        <section class="mt-8 space-y-8">
            <div class="flex items-center justify-between">
                <h2
                    class="
                        text-base
                        font-medium
                        text-gray-700 dark:text-gray-300
                    "
                >
                    Recent Discussions
                </h2>

                <a
                    href="{{ route('posts.index') }}"
                    class="
                        font-medium
                        text-gray-700 dark:text-gray-300
                        hover:underline
                    "
                >
                    Browse all →
                </a>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($recentPosts as $post)
                    <x-post-card :post="$post" compact />
                @empty
                    <div
                        class="
                            col-span-full
                            py-20
                            text-center
                            text-gray-500 dark:text-gray-400
                        "
                    >
                        No discussions yet.
                    </div>
                @endforelse
            </div>
        </section>

    </div>
</div>
@endsection
