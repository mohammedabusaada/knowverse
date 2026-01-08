@extends('layouts.app')

@section('profile-content')

    {{-- Filters --}}
    <x-activity.filters :user="$user" :type="$type" />

    {{-- Feed --}}
    @forelse ($activities as $activity)
        <x-activity.item :activity="$activity" />
    @empty
        <x-empty-state
            title="No activity yet"
            description="This user has not performed any actions yet."
        />
    @endforelse

    <div class="mt-6">
        {{ $activities->links() }}
    </div>

@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection
