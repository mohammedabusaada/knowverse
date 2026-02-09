@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <h1 class="text-2xl font-bold mb-4">{{ $user->username }}</h1>

    <p>Email: {{ $user->email }}</p>
    <p>Joined: {{ $user->created_at->diffForHumans() }}</p>

    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="mt-4">
        @csrf
        @method('DELETE')
        <button class="px-4 py-2 bg-red-600 text-white rounded">
            Delete User
        </button>
    </form>
</div>
@endsection
