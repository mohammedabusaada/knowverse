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
                    <x-post.save-post-button :post="$post" />
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

                            {{-- UPDATED: Action Dropdown --}}
                            <x-action-dropdown>
                                <x-report-button type="post" :id="$post->id" />
                                
                                @can('update', $post)
                                    <a href="{{ route('posts.edit', $post) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        Edit Post
                                    </a>
                                @endcan

                                @can('delete', $post)
                                    <hr class="my-1 border-gray-200 dark:border-gray-700">
                                    <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Delete this post permanently?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100 dark:hover:bg-gray-800">
                                            Delete Post
                                        </button>
                                    </form>
                                @endcan
                            </x-action-dropdown>
                        </div>
                    </div>

                    @if($post->is_hidden)
                        <div class="mb-6 flex items-center gap-2 p-3 bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800 rounded-lg text-red-800 dark:text-red-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span class="text-sm font-semibold">This post is hidden from the public for violating guidelines.</span>
                        </div>
                    @endif

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