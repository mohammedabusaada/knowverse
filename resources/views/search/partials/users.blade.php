@if($users->isEmpty())
    <x-search-empty icon="users" message="We couldn't find any users matching that name." />
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($users as $user)
            <a href="{{ route('profiles.show', $user->username) }}"
               class="group p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:shadow-md hover:border-blue-500/50 transition-all">
                <div class="flex items-center gap-3">
                    {{-- Avatar with group-hover effect --}}
                    <img src="{{ $user->profile_picture_url }}" 
                         alt="{{ $user->display_name }}"
                         class="w-12 h-12 rounded-full object-cover border-2 border-transparent group-hover:border-blue-500 transition-all">
                    
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 dark:text-white truncate">
                            {{ $user->display_name }}
                        </p>
                        <p class="text-xs text-gray-500 truncate">
                            {{ '@' . $user->username }}
                        </p>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $users->links() }}
    </div>
@endif