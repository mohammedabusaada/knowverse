@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto px-4 py-10">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            {{ $user->display_name }}
        </h1>

        @if (auth()->check() && auth()->id() === $user->id)
            <x-button href="{{ route('profile.edit') }}" primary>
                Edit Profile
            </x-button>
        @endif
    </div>

    <!-- Profile Card -->
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                shadow rounded-xl p-6">

        <div class="flex items-start gap-6">

            <!-- Avatar -->
            <x-user-avatar :src="$user->profile_picture_url" size="32" />

            <div class="flex-1">

                <div class="flex items-center gap-3">
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $user->display_name }}
                    </h2>

                    @if($user->academic_title)
                        <span class="text-gray-500 dark:text-gray-400 text-sm">
                            ({{ $user->academic_title }})
                        </span>
                    @endif
                </div>

                <!-- Reputation Inline -->
                <p class="text-gray-500 dark:text-gray-400 mt-2">
                    Reputation:
                    <span class="font-semibold text-gray-900 dark:text-gray-200">
                        {{ $user->reputation_points }}
                    </span>
                </p>

                <!-- Reputation Widget -->
                <div class="mt-4">
                    <x-profile.reputation-widget :user="$user" />
                </div>

                <!-- Reputation History -->
                <a href="{{ url('@' . $user->username . '/reputation') }}"
   class="inline-block mt-3 text-sm text-blue-600 dark:text-blue-400 hover:underline">
    View Reputation History →
</a>


                @if(auth()->check() && auth()->id() !== $user->id)
                    <x-button class="mt-3" primary>Follow</x-button>
                @endif

            </div>
        </div>

        <!-- Bio -->
        <div class="mt-8 border-t pt-6 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Bio</h3>

            <div class="prose dark:prose-invert max-w-none">
                @if ($user->bio)
                    <x-markdown :text="$user->bio" />
                @else
                    <p class="text-gray-500 dark:text-gray-400 italic">
                        This user hasn’t added a bio yet.
                    </p>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">

            <x-user-stat-box label="Posts" :value="$user->posts_count" />
            <x-user-stat-box label="Comments" :value="$user->all_comments_count" />
            <x-user-stat-box label="Followers" :value="$user->followers_count" />

        </div>

    </div>

</div>

@endsection
