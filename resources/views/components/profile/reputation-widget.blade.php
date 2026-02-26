@props(['user'])

@php
    $points = $user->reputation_points ?? 0;
    
    $rank = match(true) {
        $points >= 5000 => 'Sage',
        $points >= 1000 => 'Expert',
        $points >= 100 => 'Scholar',
        default => 'Novice',
    };
@endphp

<div class="flex items-center gap-5 p-5 rounded-2xl bg-white dark:bg-black border-2 border-gray-200 dark:border-gray-800 shadow-sm mb-8">
    <div class="p-3.5 bg-gray-100 dark:bg-gray-900 rounded-xl text-black dark:text-white border border-gray-200 dark:border-gray-800">
        <x-icons.chart class="w-7 h-7" />
    </div>
    
    <div>
        <div class="flex items-center gap-3">
            <span class="text-3xl font-black text-black dark:text-white leading-none">
                {{ number_format($points) }}
            </span>
            <span class="text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded bg-black text-white dark:bg-white dark:text-black">
                {{ $rank }}
            </span>
        </div>

        <div class="text-xs font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest">
            Total Reputation Points
        </div>
    </div>
</div>