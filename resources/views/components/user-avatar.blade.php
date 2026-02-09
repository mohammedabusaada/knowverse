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

// Determine image source
$finalSrc = $src ?? ($user ? $user->profile_picture_url : null);

// Determine fallback initial
$initial = $user ? substr($user->username, 0, 1) : '?';
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-full overflow-hidden border border-gray-200 dark:border-gray-700 flex-shrink-0 bg-gray-100 dark:bg-gray-800 flex items-center justify-center"]) }}>
    @if($finalSrc)
        <img src="{{ $finalSrc }}" alt="{{ $user->username ?? 'User' }}" class="w-full h-full object-cover">
    @else
        <span class="font-bold text-gray-500 uppercase">
            {{ $initial }}
        </span>
    @endif
</div>