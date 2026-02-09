@extends('layouts.app')

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection

@section('profile-content')
    <div class="max-w-4xl">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm rounded-xl p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">About</h3>
            
            <div class="prose dark:prose-invert max-w-none">
                @if ($user->bio)
                    <x-markdown :text="$user->bio" />
                @else
                    <p class="text-gray-500 italic">This user hasn’t added a bio yet.</p>
                @endif
            </div>

            {{-- Stats Grid --}}
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-user-stat-box label="Posts" :value="$user->posts_count" />
                <x-user-stat-box label="Comments" :value="$user->all_comments_count" />
                <x-user-stat-box label="Followers" :value="$user->followers_count" />
            </div>
        </div>
    </div>
@endsection