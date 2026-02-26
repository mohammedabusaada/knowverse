@props(['user', 'type' => 'all'])

@php
    $filters = [
        'all'        => 'All',
        'posts'      => 'Posts',
        'votes'      => 'Votes',
        'reputation' => 'Reputation',
    ];
@endphp

<div class="mb-8 flex flex-wrap gap-3 text-sm">
    @foreach ($filters as $key => $label)
        <a href="{{ route('profile.activity', ['user' => $user->username, 'type' => $key]) }}"
           class="px-5 py-2 rounded-lg font-bold border-2 transition-all
           {{ $type === $key
               ? 'bg-black text-white border-black dark:bg-white dark:text-black dark:border-white shadow-md'
               : 'bg-white dark:bg-black border-gray-200 dark:border-gray-800 text-gray-500 hover:border-black dark:hover:border-white hover:text-black dark:hover:text-white'
           }}">
            {{ $label }}
        </a>
    @endforeach
</div>