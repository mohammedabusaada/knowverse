@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            Dashboard
        </h1>

        <p class="text-gray-600 dark:text-gray-400 mt-2">
            Welcome back, {{ auth()->user()->display_name }}! 👋
        </p>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">

        <a href="{{ route('posts.create') }}"
            class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                  hover:shadow-md transition flex flex-col items-center text-center">

            <div class="text-3xl mb-3">✍️</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create a Post</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Share knowledge with others.</p>
        </a>

        <a href="{{ route('profiles.show', auth()->user()->username) }}"
            class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                  hover:shadow-md transition flex flex-col items-center text-center">

            <div class="text-3xl mb-3">👤</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Profile</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View your public information.</p>
        </a>

        <a href="{{ route('profile.edit') }}"
            class="p-6 bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700
                  hover:shadow-md transition flex flex-col items-center text-center">

            <div class="text-3xl mb-3">⚙️</div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Profile</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Adjust your settings.</p>
        </a>

    </div>

    {{-- Stats Section --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">

        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ auth()->user()->posts()->count() }}
            </div>
            <div class="text-gray-600 dark:text-gray-400 mt-1">Posts</div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ auth()->user()->comments()->count() }}
            </div>
            <div class="text-gray-600 dark:text-gray-400 mt-1">Comments</div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-xl shadow-sm text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ auth()->user()->followers()->count() }}
            </div>
            <div class="text-gray-600 dark:text-gray-400 mt-1">Followers</div>
        </div>

    </div>

</div>
@endsection