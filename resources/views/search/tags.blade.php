@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 animate-[fadeUp_0.8s_ease_both]">

    <div class="mb-10 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink mb-4">Tags Query</h1>
        <p class="font-serif text-lg text-muted italic">
            Matching <span class="text-ink font-bold">"{{ $q }}"</span>
        </p>
    </div>

    <div class="mb-12 max-w-2xl mx-auto">
        <x-search-bar :value="$q" />
    </div>

    @if($tags->isEmpty())
        <div class="py-16 text-center border border-dashed border-rule bg-aged/10">
            <p class="font-serif text-lg text-muted italic">No tags found.</p>
        </div>
    @else
        <div class="flex flex-wrap gap-3 justify-center">
            @foreach($tags as $tag)
                <a href="{{ route('posts.index') }}?tags[]={{ urlencode($tag->name) }}"
                   class="font-mono text-xs tracking-[0.1em] text-ink bg-aged border border-rule px-4 py-1.5 hover:bg-ink hover:text-paper transition-colors">
                    <span class="opacity-40 font-serif mr-1">§</span>{{ strtolower($tag->name) }}
                </a>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $tags->links() }}
        </div>
    @endif
</div>
@endsection