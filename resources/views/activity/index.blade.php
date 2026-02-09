@extends('layouts.app')

@section('profile-content')
    <div class="max-w-4xl">
        {{-- Filters --}}
        <x-activity.filters :user="$user" :type="request('type', 'all')" />

        {{-- Feed Card --}}
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
            @forelse ($activities as $activity)
                <x-activity.item :activity="$activity" />
            @empty
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 mb-4">
                        <x-icons.chat class="w-8 h-8 text-gray-300" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No activity yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto mt-2">
                        This user hasn't performed any actions in the Verse yet.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $activities->links() }}
        </div>
    </div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection