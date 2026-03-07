@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 animate-[fadeUp_0.8s_ease_both]">
    <div class="mb-10 border-b border-rule pb-4">
        <h1 class="font-heading text-4xl font-bold text-ink mb-2">Saved Discussions</h1>
        <p class="font-serif text-lg text-muted italic">Your personal collection of bookmarked discussions.</p>
    </div>

    @if($savedPosts->isEmpty())
        <x-search-empty message="You haven't saved any discussions yet." />
    @else
        <div class="flex flex-col border-t border-rule">
            @foreach($savedPosts as $post)
                <x-post-card :post="$post" />
            @endforeach
        </div>

        <div class="mt-12">
            {{ $savedPosts->links() }}
        </div>
    @endif
</div>
@endsection