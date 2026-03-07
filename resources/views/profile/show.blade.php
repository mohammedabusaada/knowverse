@extends('profile._layout')

@section('profile-content')
    <div class="max-w-4xl">
        {{-- Scholar Biography Section --}}
        <div class="bg-paper border border-rule shadow-sm rounded-sm p-8">
            <h3 class="text-xs font-mono font-bold text-muted mb-6 uppercase tracking-[0.2em] border-b border-rule pb-2">
                About Scholar
            </h3>
            
            {{-- Safely rendered Markdown Biography --}}
            <div class="prose dark:prose-invert max-w-none font-serif text-[15px] leading-relaxed text-ink mb-10">
                @if (isset($parsedBio) && $parsedBio)
                    {!! $parsedBio !!}
                @elseif ($user->bio)
                    <p class="text-ink">{{ $user->bio }}</p>
                @else
                    <p class="text-muted italic">This scholar hasn't added a biography yet.</p>
                @endif
            </div>

            {{-- Academic Statistics Grid --}}
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6 pt-8 border-t border-rule">
                <x-user-stat-box label="Discussions" :value="$user->posts_count ?? $user->posts()->count()" />
                <x-user-stat-box label="Responses" :value="$user->all_comments_count ?? $user->allComments()->count()" />
                <x-user-stat-box label="Followers" :value="$user->followers_count ?? $user->followers()->count()" />
            </div>
        </div>
    </div>
@endsection