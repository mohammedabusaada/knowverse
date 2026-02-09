@props(['user'])

@php
    $points = $user->reputation_points ?? 0;
    
    // Simple logic for user "Rank" titles
    $rank = match(true) {
        $points >= 5000 => ['title' => 'Sage', 'color' => 'text-purple-600 dark:text-purple-400'],
        $points >= 1000 => ['title' => 'Expert', 'color' => 'text-indigo-600 dark:text-indigo-400'],
        $points >= 100 => ['title' => 'Scholar', 'color' => 'text-blue-600 dark:text-blue-400'],
        default => ['title' => 'Novice', 'color' => 'text-gray-600 dark:text-gray-400'],
    };
@endphp

<div class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm">
    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600">
        <x-icons.chart class="w-6 h-6" />
    </div>
    
    <div>
        <div class="flex items-center gap-2">
            <span class="text-2xl font-black text-gray-900 dark:text-white leading-none">
                {{ number_format($points) }}
            </span>
            <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 {{ $rank['color'] }}">
                {{ $rank['title'] }}
            </span>
        </div>

        <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-1">
            Total Reputation Points
        </div>
    </div>
</div>