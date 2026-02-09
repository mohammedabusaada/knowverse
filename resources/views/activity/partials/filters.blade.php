@props(['user', 'type' => 'all'])

<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    @foreach(['all', 'posts', 'comments', 'votes'] as $filter)
        <a href="{{ route('profile.activity', ['user' => $user->username, 'type' => $filter]) }}" 
           class="px-4 py-1.5 rounded-full text-xs font-bold border transition-all whitespace-nowrap
           {{ (request('type', 'all') == $filter) 
                ? 'bg-indigo-600 border-indigo-600 text-white shadow-md shadow-indigo-200 dark:shadow-none' 
                : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-400 hover:border-indigo-300' 
           }}">
            {{ ucfirst($filter) }}
        </a>
    @endforeach
</div>