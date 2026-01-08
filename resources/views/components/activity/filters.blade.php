@props([
    'user',
    'type' => 'all',
])

@php
    $filters = [
        'all'        => 'All',
        'posts'      => 'Posts',
        'votes'      => 'Votes',
        'reputation' => 'Reputation',
    ];
@endphp

<div class="mb-6 flex gap-2 text-sm">

    @foreach ($filters as $key => $label)
        <a href="{{ route('activity.index', [$user->username, 'type' => $key]) }}"
           class="px-4 py-2 rounded-full border transition
           {{ $type === $key
                ? 'bg-blue-600 text-white border-blue-600'
                : 'bg-white dark:bg-gray-800
                   border-gray-300 dark:border-gray-700
                   text-gray-600 dark:text-gray-300
                   hover:bg-gray-100 dark:hover:bg-gray-700'
           }}">
            {{ $label }}
        </a>
    @endforeach

</div>
