@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Search header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold dark:text-white">Search results</h1>
        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Showing results for: <span class="font-medium">{{ $q }}</span></p>
    </div>

    {{-- Search bar (prefilled) --}}
    <div class="mb-6">
        <form action="{{ route('search') }}" method="GET" class="flex gap-2">
            <input name="q" value="{{ old('q', $q) }}" placeholder="Search posts, tags, users..."
                class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 dark:text-white"
                autofocus>
            <button class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">Search</button>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Posts column (main) --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold dark:text-white">Posts</h2>
                @if($posts instanceof \Illuminate\Support\Collection && $posts->isEmpty())
                <span class="text-sm text-gray-500 dark:text-gray-400">No results</span>
                @endif
            </div>

            @if($posts instanceof \Illuminate\Pagination\LengthAwarePaginator)
            @foreach ($posts as $post)
            <a href="{{ route('posts.show', $post) }}" class="block p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="text-lg font-semibold dark:text-white">{{ $post->title }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            by {{ $post->user->display_name }} · {{ $post->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        👁 {{ $post->view_count }}
                    </div>
                </div>

                {{-- snippet with highlight --}}
                @php
                $snippet = Str::limit(strip_tags($post->body), 220);
                $escaped = e($snippet);
                $pattern = '/' . preg_quote($q, '/') . '/i';
                $highlighted = preg_replace($pattern, '<mark class="bg-yellow-200 dark:bg-yellow-600">$0</mark>', $escaped);
                @endphp
                <p class="mt-3 text-gray-700 dark:text-gray-300 prose max-w-none">
                    {!! $highlighted !!}
                </p>
            </a>
            @endforeach

            <div class="mt-6">
                {{ $posts->links() }}
            </div>
            @else
            {{-- no posts result --}}
            <div class="text-gray-600 dark:text-gray-300">No posts found.</div>
            @endif
        </div>

        {{-- Sidebar: users + tags --}}
        <aside class="space-y-6">

            {{-- Users --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold mb-3 dark:text-white">Users</h3>

                @if($users->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No users found.</p>
                @else
                <ul class="space-y-3">
                    @foreach($users as $user)
                    <li class="flex items-center gap-3">
                        <img src="{{ $user->profile_picture_url }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <a href="{{ route('profiles.show', $user->username) }}" class="font-medium dark:text-white">{{ $user->display_name }}</a>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ '@' . $user->username }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>

            {{-- Tags --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold mb-3 dark:text-white">Tags</h3>

                @if($tags->isEmpty())
                <p class="text-sm text-gray-500 dark:text-gray-400">No tags found.</p>
                @else
                <div class="flex flex-wrap gap-2">
                    @foreach($tags as $tag)
                    <a href="{{ route('posts.index') . '?tag=' . urlencode($tag->name) }}" class="px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded text-sm dark:text-gray-200">
                        #{{ $tag->name }}
                    </a>
                    @endforeach
                </div>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection