@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10 animate-[fadeUp_0.8s_ease_both]">

    <div class="mb-10 text-center">
        <h1 class="font-heading text-4xl md:text-5xl font-bold text-ink mb-4">Scholars Search</h1>
        <p class="font-serif text-lg text-muted italic">
            Matching <span class="text-ink font-bold">"{{ $q }}"</span>
        </p>
    </div>

    <div class="mb-12 max-w-2xl mx-auto">
        <x-search-bar :value="$q" />
    </div>

    @if($users->isEmpty())
        <div class="py-16 text-center border border-dashed border-rule bg-aged/10">
            <p class="font-serif text-lg text-muted italic">No scholars found matching your criteria.</p>
        </div>
    @else
        <div class="flex flex-col border-t border-rule">
            @foreach($users as $user)
                <a href="{{ route('profile.show', $user->username) }}"
                   class="flex items-center gap-4 py-5 border-b border-rule hover:bg-aged/20 transition-colors px-2 group">

                    <x-user-avatar :src="$user->profile_picture_url" size="lg" class="border border-rule grayscale opacity-90 group-hover:grayscale-0 transition-all" />

                    <div class="min-w-0">
                        <p class="font-heading text-lg font-bold text-ink group-hover:text-accent transition-colors truncate">
                            {{ $user->display_name }}
                        </p>
                        <p class="font-mono text-xs text-muted mt-1 truncate">
                            {{ '@' . $user->username }}
                        </p>
                    </div>

                    <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity font-mono text-[9px] uppercase tracking-widest text-ink pr-4 hidden sm:block">
                        View User &rarr;
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection