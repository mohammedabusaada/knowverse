@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 animate-[fadeUp_0.8s_ease_both]">

    {{-- Search Header --}}
    <div class="mb-10 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink mb-4">Search Results</h1>
        <p class="font-serif text-lg text-muted italic">
            Found {{ number_format($counts[$type]) }} {{ str($type)->singular($counts[$type]) }} for <span class="text-ink font-bold">"{{ $q }}"</span>
        </p>
    </div>

    {{-- Search Bar Component --}}
    <div class="mb-12 max-w-2xl mx-auto">
        <x-search-bar placeholder="Search for something else..." :value="$q" />
    </div>

    <div>
        {{-- Navigation Tabs --}}
        <div class="flex justify-center gap-8 mb-12 border-b border-rule">
            @foreach (['posts' => 'Discussions', 'users' => 'Scholars', 'tags' => 'Topics'] as $key => $label)
                <a 
                    href="{{ route('search', array_merge(request()->query(), ['type' => $key])) }}"
                    class="pb-3 text-sm font-mono uppercase tracking-[0.15em] transition-colors border-b-2 {{ $type === $key 
                        ? 'border-ink text-ink font-bold' 
                        : 'border-transparent text-muted hover:text-ink' 
                    }}"
                >
                    {{ $label }} <sup class="text-[9px] opacity-70 ml-0.5">{{ $counts[$key] }}</sup>
                </a>
            @endforeach
        </div>

        {{-- Dynamic Results --}}
        <div class="bg-paper border border-rule shadow-sm p-2 sm:p-6 rounded-sm">
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