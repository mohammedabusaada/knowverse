@props(['user' => null, 'src' => null, 'size' => 'md'])

@php
// Define responsive size presets, including a huge one for the profile page
$sizes = [
    'xs'  => 'w-6 h-6 text-[10px]',
    'sm'  => 'w-8 h-8 text-xs',
    'md'  => 'w-10 h-10 text-sm',
    'lg'  => 'w-16 h-16 text-xl',
    'xl'  => 'w-24 h-24 text-2xl',
    '2xl' => 'w-28 h-28 md:w-32 md:h-32 text-4xl', // Specifically for Profile Header
];

$sizeClass = $sizes[$size] ?? $sizes['md'];

// Strict validation: Check if a picture explicitly exists in the database
// This prevents resolving empty asset paths like "http://domain.com/storage/"
$hasValidImage = !empty($src) || ($user && !empty($user->profile_picture));

$finalSrc = $src ?? ($user ? $user->profile_picture_url : null);

// Extract the first letter for the fallback (prefers display_name over username)
$initial = '?';
if ($user) {
    $initial = mb_substr($user->display_name ?? $user->username, 0, 1);
}
@endphp

<div {{ $attributes->merge(['class' => "$sizeClass rounded-full overflow-hidden shrink-0 bg-aged flex items-center justify-center border border-rule"]) }}>
    @if($hasValidImage)
        <img src="{{ $finalSrc }}" alt="{{ $user->username ?? 'Avatar' }}" class="w-full h-full object-cover">
    @else
        <span class="font-heading font-bold text-ink uppercase leading-none mt-1">
            {{ $initial }}
        </span>
    @endif
</div>