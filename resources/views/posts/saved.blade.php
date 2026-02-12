@extends('layouts.app')

@section('title', 'Saved Posts')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Saved Posts</h1>

    @if($savedPosts->isEmpty())
        {{-- Empty state when no saved posts --}}
        <x-empty-state message="You haven't saved any posts yet" />
    @else
        {{-- Grid layout for saved posts --}}
        <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($savedPosts as $post)
                <x-post-card :post="$post" />
            @endforeach
        </div>
    @endif

    {{-- Skeleton loader example (optional) --}}
    {{--
    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 animate-pulse">
        @for ($i = 0; $i < 6; $i++)
            <div class="bg-gray-200 h-64 rounded-lg"></div>
        @endfor
    </div>
    --}}
</div>
@endsection
