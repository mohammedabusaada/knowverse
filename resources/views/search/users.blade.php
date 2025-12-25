@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold dark:text-white mb-2">
        Users matching “{{ $q }}”
    </h1>

    <div class="mb-6">
        <x-search-bar :value="$q" />
    </div>

    @if($users->isEmpty())
        <p class="text-gray-600 dark:text-gray-300">
            No users found.
        </p>
    @else
        <div class="space-y-4">
            @foreach($users as $user)
                <a href="{{ route('profiles.show', $user->username) }}"
                   class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800
                          border border-gray-200 dark:border-gray-700
                          rounded-lg hover:shadow transition">

                    <img src="{{ $user->profile_picture_url }}"
                         class="w-12 h-12 rounded-full object-cover">

                    <div>
                        <p class="font-semibold dark:text-white">
                            {{ $user->display_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ '@' . $user->username }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
