@extends('layouts.app')

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection

@section('profile-content')
    <div class="max-w-4xl">
        <div class="bg-white dark:bg-black border-2 border-gray-200 dark:border-gray-800 shadow-sm rounded-2xl p-8">
            <h3 class="text-xl font-black text-black dark:text-white mb-6 uppercase tracking-widest">About</h3>
            
            <div class="prose dark:prose-invert max-w-none font-medium leading-relaxed">
                @if ($user->bio)
                    <x-markdown :text="$user->bio" />
                @else
                    <p class="text-gray-500 italic">This user hasn't added a bio yet.</p>
                @endif
            </div>

            {{-- Stats Grid --}}
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6 pt-8 border-t-2 border-gray-100 dark:border-gray-800">
                <x-user-stat-box label="Posts" :value="$user->posts_count" />
                <x-user-stat-box label="Comments" :value="$user->all_comments_count" />
                <x-user-stat-box label="Followers" :value="$user->followers_count" />
            </div>
        </div>
    </div>
@endsection