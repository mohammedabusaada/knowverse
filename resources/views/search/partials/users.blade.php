@if($users->isEmpty())
    <x-search-empty icon="users" message="We couldn't find any users matching that name." />
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($users as $user)
            <a href="{{ route('profile.show', $user->username) }}"
               class="group flex items-center gap-4 p-5 bg-white dark:bg-black border-2 border-gray-200 dark:border-gray-800 rounded-2xl hover:border-black dark:hover:border-white transition-all shadow-sm">
                
                <img src="{{ $user->profile_picture_url }}" 
                     alt="{{ $user->display_name }}"
                     class="w-14 h-14 rounded-full object-cover border-2 border-transparent group-hover:border-black dark:group-hover:border-white transition-all shrink-0">
                
                <div class="min-w-0">
                    <p class="font-black text-black dark:text-white truncate text-lg">
                        {{ $user->display_name }}
                    </p>
                    <p class="text-xs font-bold text-gray-500 truncate mt-0.5">
                        {{ '@' . $user->username }}
                    </p>
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-10">
        {{ $users->links() }}
    </div>
@endif