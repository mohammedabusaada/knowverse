@extends('layouts.app')

@section('profile-content')
<div class="max-w-4xl" x-data="{ 
    tab: 'people', 
    search: '',
    matches(text) {
        return text.toLowerCase().includes(this.search.toLowerCase())
    }
}">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h3 class="text-2xl font-black text-black dark:text-white">Following</h3>
            <p class="text-sm font-medium text-gray-500 mt-1">Manage the people and topics you follow</p>
        </div>
        
        <div class="flex flex-col sm:flex-row items-center gap-3">
            {{-- Search Bar --}}
            <div class="relative w-full sm:w-auto">
                <input type="text" 
                       x-model="search" 
                       placeholder="Search following..." 
                       class="w-full sm:w-64 pl-10 pr-4 py-2 bg-white dark:bg-black border-2 border-gray-200 dark:border-gray-800 rounded-xl text-sm font-medium focus:ring-0 focus:border-black dark:focus:border-white transition-all text-black dark:text-white">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex bg-gray-100 dark:bg-gray-800 p-1 rounded-xl shrink-0">
                <button @click="tab = 'people'" 
                    :class="tab === 'people' ? 'bg-white dark:bg-black shadow-sm text-black dark:text-white border border-gray-200 dark:border-gray-700' : 'text-gray-500 border border-transparent'"
                    class="px-5 py-1.5 text-xs font-bold uppercase tracking-widest rounded-lg transition-all">
                    People
                </button>
                <button @click="tab = 'tags'" 
                    :class="tab === 'tags' ? 'bg-white dark:bg-black shadow-sm text-black dark:text-white border border-gray-200 dark:border-gray-700' : 'text-gray-500 border border-transparent'"
                    class="px-5 py-1.5 text-xs font-bold uppercase tracking-widest rounded-lg transition-all">
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($following as $followedUser)
                    <div x-show="matches('{{ $followedUser->display_name }} {{ $followedUser->username }}')">
                        <x-user-card :user="$followedUser" />
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center text-gray-500 font-medium">Not following any people yet.</div>
                @endforelse
            </div>
            <div class="mt-8" x-show="search === ''">{{ $following->appends(['tab' => 'people'])->links() }}</div>
        </div>

        {{-- Tab 2: Tags --}}
        <div x-show="tab === 'tags'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @forelse($followingTags as $tag)
                    <div x-show="matches('{{ $tag->name }}')" 
                         class="bg-white dark:bg-black p-5 rounded-2xl border-2 border-gray-200 dark:border-gray-800 flex items-center justify-between group hover:border-black dark:hover:border-white transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 flex-none bg-gray-100 dark:bg-gray-900 rounded-xl flex items-center justify-center">
                                <x-icons.tag class="w-6 h-6 text-black dark:text-white" />
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('tags.show', $tag->slug) }}" class="font-black text-black dark:text-white hover:underline block truncate text-lg">
                                    #{{ $tag->name }}
                                </a>
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">{{ $tag->posts_count ?? 0 }} posts</p>
                            </div>
                        </div>

                        @if(auth()->id() === $user->id)
                            <button x-data="{ unfollowed: false }" 
                                    x-show="!unfollowed"
                                    @click="fetch('{{ route('tags.unfollow', $tag) }}', {method: 'DELETE', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}' }}); unfollowed = true"
                                    class="text-xs font-bold text-gray-400 hover:text-red-500 transition-colors uppercase tracking-widest">
                                Unfollow
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="col-span-2 py-12 text-center text-gray-500 font-medium">Not following any tags yet.</div>
                @endforelse
            </div>
            <div class="mt-8" x-show="search === ''">{{ $followingTags->appends(['tab' => 'tags'])->links() }}</div>
        </div>
    @endif
</div>
@endsection

@section('content')
    @include('profile._layout', ['user' => $user])
@endsection