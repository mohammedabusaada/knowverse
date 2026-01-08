@extends('layouts.app')

@section('content')
    @include('profile._layout', ['user' => $user])

    @section('profile-content') {{-- Match the @yield name in _layout --}}
        <div class="max-w-4xl mx-auto py-10">
            <h1 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white">
                Reputation History — {{ $user->display_name }}
            </h1>

            @include('reputation.partials.table', ['history' => $history])
        </div>
    @endsection
@endsection