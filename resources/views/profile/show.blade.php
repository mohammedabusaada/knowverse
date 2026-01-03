@extends('layouts.app')

@section('content')
    @include('profile._layout', ['user' => $user])

    @section('profile-content') {{-- Change @slot to @section --}}
        <div class="prose dark:prose-invert max-w-none">
            {!! nl2br(e($user->bio)) !!}
            
            {{-- Add your stats/reputation widget here if you want them inside the tab area --}}
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-user-stat-box label="Posts" :value="$user->posts_count" />
                <x-user-stat-box label="Comments" :value="$user->all_comments_count" />
                <x-user-stat-box label="Followers" :value="$user->followers_count" />
            </div>
        </div>
    @endsection {{-- Change @endslot to @endsection --}}
@endsection