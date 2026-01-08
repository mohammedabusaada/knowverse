@extends('layouts.app')

{{-- 1. DEFINE the tab content first --}}
@section('profile-content')
    <div class="max-w-4xl mx-auto py-6">

        {{-- Edit Button --}}
        @if (auth()->check() && auth()->id() === $user->id)
            <div class="flex justify-end mb-4">
                <x-button href="{{ route('profile.edit') }}" primary>
                    Edit Profile
                </x-button>
            </div>
        @endif

        {{-- The Data Card (Avatar, Bio, Stats) --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow rounded-xl p-6">
            <div class="flex items-start gap-6">
                <x-user-avatar :src="$user->profile_picture_url" size="32" />

                <div class="flex-1">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user->display_name }}
                        </h2>
                        @if($user->academic_title)
                            <span class="text-gray-500 text-sm">({{ $user->academic_title }})</span>
                        @endif
                    </div>

                    <p class="text-gray-500 mt-2">
                        Reputation: <span class="font-semibold text-gray-900 dark:text-gray-200">{{ $user->reputation_points }}</span>
                    </p>

                    <div class="mt-4">
                        <x-profile.reputation-widget :user="$user" />
                    </div>
                </div>
            </div>

            {{-- Bio --}}
            <div class="mt-8 border-t pt-6 dark:border-gray-700">
                <h3 class="text-lg font-semibold mb-2">Bio</h3>
                <div class="prose dark:prose-invert max-w-none">
                    @if ($user->bio)
                        <x-markdown :text="$user->bio" />
                    @else
                        <p class="text-gray-500 italic">This user hasn’t added a bio yet.</p>
                    @endif
                </div>
            </div>

            {{-- Stats --}}
            <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <x-user-stat-box label="Posts" :value="$user->posts_count" />
                <x-user-stat-box label="Comments" :value="$user->all_comments_count" />
                <x-user-stat-box label="Followers" :value="$user->followers_count" />
            </div>
        </div>
    </div>
@endsection

{{-- 2. NOW call the main layout and include the sub-layout --}}
@section('content')
    @include('profile._layout', ['user' => $user])
@endsection