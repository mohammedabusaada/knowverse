@props([
    'user',
    'type' => 'all',
])

@php
    $filters = [
        'all'        => 'Overview',
        'posts'      => 'Discussions',
        'comments'   => 'Replies',
    ];
@endphp

<div class="mb-8 border-b border-rule flex flex-wrap gap-6 text-sm">
    @foreach ($filters as $key => $label)
        <a href="{{ route('profile.activity', ['user' => $user->username, 'type' => $key]) }}"
           class="pb-2 font-mono text-[10px] uppercase tracking-[0.15em] transition-colors border-b-2 
           {{ $type === $key
               ? 'border-ink text-ink font-bold'
               : 'border-transparent text-muted hover:text-ink hover:border-rule'
           }}">
            {{ $label }}
        </a>
    @endforeach
</div>