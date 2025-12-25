@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold dark:text-white mb-2">
        Tags matching “{{ $q }}”
    </h1>

    <div class="mb-6">
        <x-search-bar :value="$q" />
    </div>

    @if($tags->isEmpty())
        <p class="text-gray-600 dark:text-gray-300">
            No tags found.
        </p>
    @else
        <div class="flex flex-wrap gap-3">
            @foreach($tags as $tag)
                <a href="{{ route('posts.index') }}?tags[]={{ urlencode($tag->name) }}"
                   class="px-4 py-2 rounded-lg
                          bg-gray-100 dark:bg-gray-700
                          text-gray-800 dark:text-gray-200
                          hover:bg-gray-200 dark:hover:bg-gray-600">
                    #{{ $tag->name }}
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $tags->links() }}
        </div>
    @endif
</div>
@endsection
