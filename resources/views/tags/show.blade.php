@extends('layouts.app')

@section('content')
<div class="space-y-6">
    {{-- Tag Header Component --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-sm"
         x-data="{ 
            isFollowing: {{ $isFollowing ? 'true' : 'false' }}, 
            count: {{ $tag->followers_count ?? 0 }},
            loading: false,
            toggleFollow() {
                if (this.loading) return;
                this.loading = true;
                const originalState = this.isFollowing;
                this.isFollowing = !this.isFollowing;
                this.count = this.isFollowing ? this.count + 1 : this.count - 1;

                fetch(this.isFollowing ? '{{ route('tags.follow', $tag) }}' : '{{ route('tags.unfollow', $tag) }}', {
                    method: this.isFollowing ? 'POST' : 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                })
                .then(response => { if (!response.ok) throw new Error(); })
                .catch(() => {
                    this.isFollowing = originalState;
                    this.count = this.isFollowing ? this.count + 1 : this.count - 1;
                    alert('Error updating follow status.');
                })
                .finally(() => this.loading = false);
            }
         }">
        
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-4">
                {{-- Fixed Icon Container --}}
                <div class="w-14 h-14 flex-none bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center border border-indigo-100 dark:border-indigo-800">
                    <x-icons.tag class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                </div>

                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white truncate">#{{ $tag->name }}</h1>
                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-bold rounded-full border border-gray-200 dark:border-gray-600">
                            {{-- Fallback values inside spans for better SEO/Initial Load --}}
                            <span x-text="count">{{ $tag->followers_count ?? 0 }}</span> 
                            <span x-text="count === 1 ? 'Follower' : 'Followers'">{{ Str::plural('Follower', $tag->followers_count ?? 0) }}</span>
                        </span>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        Browsing {{ number_format($posts->total()) }} posts tagged with this topic.
                    </p>
                </div>
            </div>
            
            @auth
                <button @click="toggleFollow()" 
                        :class="isFollowing 
                            ? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600' 
                            : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-md shadow-indigo-200 dark:shadow-none'"
                        class="px-8 py-2.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 active:scale-95 disabled:opacity-50"
                        :disabled="loading">
                    
                    {{-- Loading Spinner SVG --}}
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>

                    <span x-text="isFollowing ? 'Unfollow' : 'Follow Tag'"></span>
                </button>
            @endauth
        </div>
    </div>

    {{-- Posts Feed --}}
    <div class="space-y-4">
        @forelse($posts as $post)
            <x-post-card :post="$post" />
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="inline-flex p-4 bg-gray-50 dark:bg-gray-900 rounded-full mb-4">
                    <x-icons.tag class="w-8 h-8 text-gray-400" />
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">No posts yet</h3>
                <p class="text-gray-500 max-w-xs mx-auto mt-2">Be the first to share something about #{{ $tag->name }}!</p>
                <div class="mt-6">
                    <a href="{{ route('posts.create', ['tag' => $tag->name]) }}" class="text-indigo-600 font-semibold hover:underline">
                        Create a Post &rarr;
                    </a>
                </div>
            </div>
        @endforelse

        <div class="py-6">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection