@extends('layouts.app')

@section('profile-content')
    <div class="max-w-4xl">
        {{-- Filters --}}
        <x-activity.filters :user="$user" :type="request('type', 'all')" />

        {{-- Feed Card --}}
        <div class="bg-white dark:bg-black border-2 border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
            @forelse ($activities as $activity)
                @include('activity._item', ['activity' => $activity])
            @empty
                <div class="p-16 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-900 mb-5 border border-gray-200 dark:border-gray-800">
                        <x-icons.chat class="w-8 h-8 text-black dark:text-white" />
                    </div>
                    <h3 class="text-xl font-black text-black dark:text-white">No activity yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-xs mx-auto mt-2 font-medium">
                        This user hasn't performed any actions in the Verse yet.
                    </p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $activities->links() }}
        </div>
    </div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection