@extends('layouts.app')

@section('profile-content')
    <div class="max-w-4xl py-6">
        <div class="flex flex-col gap-6">
            <header>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    Reputation Dashboard
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Track how your contributions impact the community.
                </p>
            </header>

            {{-- Show the current rank and points at the top --}}
            <x-profile.reputation-widget :user="$user" />

            <div class="space-y-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white px-1">
                    Recent History
                </h2>
                @include('reputation.partials.table', ['history' => $history])
            </div>
        </div>
    </div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection