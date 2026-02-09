@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto" x-data="{ search: '' }">
    {{-- Header & Search --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div class="max-w-xl">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight">Explore Tags</h1>
            <p class="text-lg text-gray-500 dark:text-gray-400 mt-2">
                Follow your favorite topics to personalize your feed.
            </p>
        </div>

        {{-- Client-side Search Filter --}}
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input 
                x-model="search"
                type="text" 
                placeholder="Filter tags..." 
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 dark:border-gray-700 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all shadow-sm"
            >
        </div>
    </div>

    {{-- Tags Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($tags as $tag)
            {{-- Check if logged in user follows this tag --}}
            @php
                $isFollowing = auth()->check() && auth()->user()->followedTags->contains($tag->id);
            @endphp

            <div 
                x-show="'{{ strtolower($tag->name) }}'.includes(search.toLowerCase())"
                x-data="{ 
                    following: {{ $isFollowing ? 'true' : 'false' }},
                    toggle() {
                        this.following = !this.following;
                        fetch('{{ route('tags.follow', $tag) }}', {
                            method: this.following ? 'POST' : 'DELETE',
                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                        }).catch(() => this.following = !this.following);
                    }
                }"
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl p-6 hover:shadow-xl hover:border-indigo-500/50 transition-all group relative overflow-hidden"
            >
                {{-- Decorative Background Glow --}}
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/10 transition-all"></div>

                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl">
                        <x-icons.tag class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>

                    @auth
                        <button 
                            @click="toggle()"
                            :class="following ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' : 'bg-indigo-600 text-white shadow-md shadow-indigo-200 dark:shadow-none'"
                            class="text-xs font-bold px-4 py-2 rounded-lg transition-all active:scale-95"
                        >
                            <span x-text="following ? 'Following' : 'Follow'"></span>
                        </button>
                    @endauth
                </div>

                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    <a href="{{ route('tags.show', $tag->slug) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        #{{ $tag->name }}
                    </a>
                </h2>
                
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2 line-clamp-2 leading-relaxed">
                    {{ $tag->description ?? 'Join the community discussing ' . $tag->name . '. Share knowledge and solve problems together.' }}
                </p>

                <div class="mt-6 flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-700/50">
                    <span class="text-sm font-medium text-gray-400">
                        {{ number_format($tag->posts_count) }} posts
                    </span>
                    <a href="{{ route('tags.show', $tag->slug) }}" class="text-sm font-bold text-indigo-600 dark:text-indigo-400 flex items-center gap-1 group/link">
                        Explore 
                        <span class="transform group-hover/link:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-12">
        {{ $tags->links() }}
    </div>
</div>
@endsection