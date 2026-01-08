@extends('layouts.app')
@extends('profile._layout')

@section('content')
    {{-- Empty on purpose --}}
@endsection

@section('profile-content')
    <div class="prose dark:prose-invert max-w-none">
        {!! nl2br(e($user->bio)) !!}

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <x-user-stat-box label="Posts" :value="$user->posts_count" />
            <x-user-stat-box label="Comments" :value="$user->all_comments_count" />
            <x-user-stat-box label="Followers" :value="$user->followers_count" />
        </div>
    </div>
@endsection
