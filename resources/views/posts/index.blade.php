@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 animate-[fadeUp_0.8s_ease_both]">

    {{-- Header --}}
    <div class="flex items-center gap-4 mb-10">
        <h1 class="font-mono text-xs tracking-[0.2em] uppercase text-muted m-0">The Archive</h1>
        <div class="flex-1 h-px bg-rule"></div>
    </div>

    {{-- Active Filters Banner --}}
    @if(!empty($selectedTags))
        <div class="mb-8 p-4 bg-aged/30 border border-rule flex flex-wrap items-center gap-3">
            <span class="font-mono text-[10px] uppercase tracking-widest text-muted">Filtered by:</span>
            
            @foreach($selectedTags as $tag)
                <span class="font-mono text-[11px] tracking-wider text-ink bg-aged border border-rule px-2 py-0.5">
                    {{ strtolower($tag) }}
                </span>
            @endforeach

            <a href="{{ route('posts.index') }}" class="ml-auto font-mono text-[10px] uppercase tracking-widest text-accent hover:text-ink transition-colors">
                [ Clear Filters ]
            </a>
        </div>
    @endif

    <div class="flex flex-col md:flex-row gap-12">
        
        {{-- Main Feed --}}
        <div class="flex-1 min-w-0">
            @if ($posts->isEmpty())
                <div class="text-center py-20 text-muted italic border border-dashed border-rule">
                    The archive yielded no results.
                </div>
            @else
                <div class="flex flex-col">
                    @foreach ($posts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

        {{-- Sidebar Filters (Desktop) --}}
        <aside class="hidden md:block w-64 shrink-0">
            <div class="sticky top-24">
                @include('posts.partials.filters')
            </div>
        </aside>
    </div>

</div>

<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection