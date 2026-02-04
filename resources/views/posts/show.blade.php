@extends('layouts.app')

@section('content')
    {{-- 1. Delegated to a component --}}
    <x-post.progress-bar />

    <div class="max-w-7xl mx-auto px-4 py-8 lg:flex lg:gap-8">
        <div class="lg:w-3/4">
            
            {{-- Header --}}
            <div class="mb-6">
                <nav class="flex text-xs uppercase tracking-widest text-gray-500 mb-4">
                    <a href="{{ route('posts.index') }}" class="hover:text-blue-600 transition">Posts</a>
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-gray-400">Discussion</span>
                </nav>
                <h1 class="text-2xl md:text-3xl font-semibold text-gray-900 dark:text-gray-100">
                    {{ $post->title }}
                </h1>
            </div>

            {{-- Main Post Card --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 md:p-6 shadow-sm flex gap-4">
                {{-- Left Side: Voting --}}
                <div class="flex flex-col items-center">
                    <x-post-vote :post="$post" />
                    <x-post.bookmark-button />
                </div>

                <div class="flex-1">
                    <div class="flex items-center border-b border-gray-200 dark:border-gray-800 pb-4 mb-6">
                        <x-user-avatar :src="$post->user->profile_picture_url" size="xs" />
                        <x-user-hover-card :user="$post->user" class="ml-2" />
                        <span class="mx-2 text-gray-300">•</span>
                        <span class="text-gray-500 text-sm">{{ $post->created_at->diffForHumans() }}</span>
                        
                        <div class="ml-auto flex items-center gap-4">
                            <span class="text-gray-500 text-xs flex items-center gap-1">
                                <x-icons.eye class="w-4 h-4" /> {{ number_format($post->view_count) }}
                            </span>
                            <x-post.share-button />
                        </div>
                    </div>

                    <article class="prose dark:prose-invert max-w-none">
                        <x-markdown :text="$post->body" />
                    </article>
                </div>
            </div>

            {{-- Comments Section --}}
            <section class="mt-8">
                <x-post.comment-section :post="$post" :comments="$comments" />
            </section>
        </div>

        {{-- Sidebar --}}
        <aside class="hidden lg:block lg:w-1/4">
            <x-post.sidebar-author :user="$post->user" />
        </aside>
    </div>
@endsection