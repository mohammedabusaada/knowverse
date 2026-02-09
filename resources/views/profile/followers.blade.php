@extends('layouts.app')

@section('profile-content')
<div class="max-w-4xl" x-data="{ 
    search: '',
    matches(text) {
        return text.toLowerCase().includes(this.search.toLowerCase())
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Followers</h3>
            <p class="text-sm text-gray-500">People following {{ $user->display_name }}</p>
        </div>

        @if(!$isPrivate)
        <div class="relative">
            <input type="text" 
                   x-model="search" 
                   placeholder="Search followers..." 
                   class="w-full md:w-64 pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
        @endif
    </div>

    @if($isPrivate)
        <x-empty-state 
            icon="icons.eye-off" 
            title="This list is private" 
            subtitle="Only {{ $user->display_name }} can see who follows them."
        />
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($followers as $follower)
                <div x-show="matches('{{ $follower->display_name }} {{ $follower->username }}')">
                    <x-user-card :user="$follower" />
                </div>
            @empty
                <div class="col-span-2 py-10 text-center text-gray-500 italic">
                    No followers yet.
                </div>
            @endforelse
        </div>

        @if($followers->hasPages())
            <div class="mt-6" x-show="search === ''">
                {{ $followers->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection