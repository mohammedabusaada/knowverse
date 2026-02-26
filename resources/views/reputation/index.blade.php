@extends('layouts.app')

@section('profile-content')
    <div class="max-w-4xl py-6 animate-[fadeUp_0.8s_ease_both]">
        <div class="flex flex-col gap-8">
            <header class="border-b border-rule pb-4">
                <h1 class="font-heading text-3xl font-bold text-ink mb-2">
                    Academic Standing
                </h1>
                <p class="font-serif text-[15px] text-muted italic">
                    A record of your contributions and standing within the KnowVerse community.
                </p>
            </header>

            {{-- Show the current rank and points at the top --}}
            <x-profile.reputation-widget :user="$user" />

            <div class="space-y-6">
                <h2 class="font-mono text-[10px] uppercase tracking-[0.2em] text-muted border-b border-rule pb-2">
                    Ledger of History
                </h2>
                @include('reputation.partials.table', ['history' => $history])
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection