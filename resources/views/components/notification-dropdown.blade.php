<div class="relative" x-data="{ open: false, loading: false }">
    <div 
        x-show="open" 
        x-transition.opacity
        class="fixed inset-0 bg-black/40 backdrop-blur-sm z-40">
    </div>

    <button 
        @click="open = !open; if(open) { loading = true; setTimeout(() => loading = false, 500); }" 
        class="relative z-50 p-2 text-gray-400 transition-colors hover:text-white focus:outline-none"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>

        @if ($unreadCount > 0)
        <span class="absolute top-1 right-1 flex items-center justify-center w-4 h-4 text-[10px] font-bold text-white bg-red-500 border-2 border-black rounded-full">
            {{ $unreadCount }}
        </span>
        @endif
    </button>

    <div 
        x-show="open" 
        @click.outside="open = false" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 scale-95"
        class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-2xl rounded-xl overflow-hidden z-50"
    >
        <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Activity</h3>
            <button class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">Mark all read</button>
        </div>

        <div x-show="loading" class="p-4 space-y-3 animate-pulse">
            <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>

        <div x-show="!loading" class="max-h-[400px] overflow-y-auto custom-scrollbar">
            @forelse ($notifications as $notification)
                @include('notifications.partials.notification-item', ['notification' => $notification])
            @empty
                <div class="p-10 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">All caught up! 🎉</p>
                </div>
            @endforelse
        </div>

        <div class="p-3 text-center border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('notifications.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                See all notifications
            </a>
        </div>
    </div>
</div>