@extends('layouts.app')

@section('profile-content')
<div class="max-w-4xl" x-data="{ 
    tab: 'people', 
    search: '',
    {{-- This helper checks if an element matches the search string --}}
    matches(text) {
        return text.toLowerCase().includes(this.search.toLowerCase())
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Following</h3>
            <p class="text-sm text-gray-500">Manage the people and topics you follow</p>
        </div>
        
        <div class="flex items-center gap-3">
            {{-- Search Bar --}}
            <div class="relative">
                <input type="text" 
                       x-model="search" 
                       placeholder="Search following..." 
                       class="w-full md:w-64 pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-indigo-500 transition-all">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-xl">
                <button @click="tab = 'people'" 
                    :class="tab === 'people' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-gray-500'"
                    class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all">
                    People
                </button>
                <button @click="tab = 'tags'" 
                    :class="tab === 'tags' ? 'bg-white dark:bg-gray-700 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-gray-500'"
                    class="px-4 py-1.5 text-sm font-bold rounded-lg transition-all">
                    Tags
                </button>
            </div>
        </div>
    </div>

    @if($isPrivate)
        <x-empty-state icon="icons.eye-off" title="This list is private" subtitle="Only the user can see this." />
    @else
        {{-- Tab 1: People --}}
        <div x-show="tab === 'people'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($following as $followedUser)
                    {{-- Alpine filter: hides if it doesn't match search --}}
                    <div x-show="matches('{{ $followedUser->display_name }} {{ $followedUser->username }}')">
                        <x-user-card :user="$followedUser" />
                    </div>
                @empty
                    <div class="col-span-2 py-10 text-center text-gray-500 italic">Not following any people yet.</div>
                @endforelse
            </div>
            <div class="mt-6" x-show="search === ''">{{ $following->appends(['tab' => 'people'])->links() }}</div>
        </div>

        {{-- Tab 2: Tags --}}
        <div x-show="tab === 'tags'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($followingTags as $tag)
                    <div x-show="matches('{{ $tag->name }}')" 
                         class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 flex-none bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center border border-indigo-100 dark:border-indigo-800">
                                <x-icons.tag class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tags.show', $tag->slug) }}" class="font-bold text-gray-900 dark:text-white hover:text-indigo-600 block truncate">
                                    #{{ $tag->name }}
                                </a>
                                <p class="text-xs text-gray-500">{{ $tag->posts_count ?? 0 }} posts</p>
                            </div>
                        </div>

                        @if(auth()->id() === $user->id)
                            <button x-data="{ unfollowed: false }" 
                                    x-show="!unfollowed"
                                    @click="fetch('{{ route('tags.unfollow', $tag) }}', {method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' }}); unfollowed = true"
                                    class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors">
                                Unfollow
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 py-10 text-center text-gray-500 italic">Not following any tags yet.</div>
                @endforelse
            </div>
            <div class="mt-6" x-show="search === ''">{{ $followingTags->appends(['tab' => 'tags'])->links() }}</div>
        </div>
    @endif
</div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection