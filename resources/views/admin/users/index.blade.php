@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    <div class="bg-white rounded shadow">
        @foreach($users as $user)
            <div class="p-4 border-b flex justify-between">
                <span>{{ $user->username }}</span>
                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600">
                    View
                </a>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
</div>
@endsection
