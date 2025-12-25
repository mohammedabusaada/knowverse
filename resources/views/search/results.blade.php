@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="mb-6">
    <h1 class="text-2xl font-bold dark:text-white">Search results</h1>
    <div class="flex items-center gap-2 mt-1">
        <p class="text-sm text-gray-600 dark:text-gray-300">
            Found {{ number_format($counts[$type]) }} {{ str($type)->singular($counts[$type]) }} 
            for <span class="font-semibold text-blue-600">"{{ $q }}"</span>
        </p>
    </div>
</div>

    {{-- Search bar --}}
    <div class="mb-6">
        <x-search-bar
            placeholder="Search posts, users, tags..."
            :value="$q" />
    </div>

    {{-- Active Tag Filters --}}
    @if(!empty($selectedTags))
        <div class="flex flex-wrap gap-2 mb-6 items-center">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Active Tags:</span>
            @foreach($selectedTags as $tagName)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                    #{{ $tagName }}
                    <a href="{{ route('search', array_merge(request()->query(), ['tags' => array_diff($selectedTags, [$tagName])])) }}" class="ml-2 hover:text-red-500 transition-colors">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/></svg>
                    </a>
                </span>
            @endforeach
            <a href="{{ route('search', ['q' => $q]) }}" class="text-xs text-gray-500 hover:text-red-500 underline ml-2">Clear all</a>
        </div>
    @endif

    <div x-data="searchTabs('{{ $type }}')">
        {{-- Tabs --}}
        <div class="mb-8 border-b dark:border-gray-700 flex gap-6">
            @foreach (['posts' => 'Posts', 'users' => 'Users', 'tags' => 'Tags'] as $key => $label)
                <a 
                    {{-- array_merge keeps our 'tags[]' in the URL when switching tabs --}}
                    href="{{ route('search', array_merge(request()->query(), ['type' => $key])) }}"
                    class="pb-3 font-medium transition border-b-2 {{ $type === $key 
                        ? 'border-blue-600 text-blue-600' 
                        : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' 
                    }}"
                >
                    {{ $label }} 
                    <span class="text-xs ml-1 opacity-70">({{ $counts[$key] }})</span>
                </a>
            @endforeach
        </div>

        {{-- Results Content --}}
        <div class="mt-6">
            @if($type === 'posts')
                @include('search.partials.posts')
            @elseif($type === 'users')
                @include('search.partials.users')
            @elseif($type === 'tags')
                @include('search.partials.tags')
            @endif
        </div>
    </div>
</div>
@endsection