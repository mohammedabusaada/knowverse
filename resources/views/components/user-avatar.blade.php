@props(['user' => null, 'src' => null, 'size' => 'md'])

@php
$sizes = [
    'xs' => 'w-6 h-6 text-[10px]',
    'sm' => 'w-8 h-8 text-xs',
    'md' => 'w-10 h-10 text-sm',
    'lg' => 'w-16 h-16 text-xl',
    'xl' => 'w-24 h-24 text-2xl',
];

$sizeClass = $sizes[$size] ?? $sizes['md'];

$finalSrc = $src ?? ($user ? $user->profile_picture_url : null);
$initial = $user ? substr($user->username, 0, 1) : '?';
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-full overflow-hidden shrink-0 bg-aged flex items-center justify-center"]) }}>
    @if($finalSrc)
        <img src="{{ $finalSrc }}" alt="{{ $user->username ?? 'User' }}" class="w-full h-full object-cover">
    @else
        <span class="font-heading font-bold text-ink uppercase">
            {{ $initial }}
        </span>
    @endif
</div>