@props(['user'])

<div x-data="{ 
        open: false, 
        timer: null,
        show() {
            clearTimeout(this.timer);
            this.open = true;
        },
        hide() {
            {{-- A 300ms delay gives the user time to move the mouse into the window --}}
            this.timer = setTimeout(() => { this.open = false }, 300);
        }
     }" 
     class="relative inline-block"
     @mouseenter="show()" 
     @mouseleave="hide()">
    
    <a href="{{ route('profiles.show', $user->username) }}" 
       class="font-bold text-blue-600 dark:text-blue-400 hover:underline decoration-2 underline-offset-2 transition-all">
        {{ $user->display_name }}
    </a>

    <div x-show="open" 
         {{-- This @mouseenter here is key: it cancels the 'hide' timer if the mouse enters the card --}}
         @mouseenter="show()"
         @mouseleave="hide()"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-cloak
         {{-- 
            'pt-3' creates an invisible bridge between the link and the card 
            so the mouse never leaves the 'active' area.
         --}}
         class="absolute bottom-full left-0 z-[9999] pt-3 w-80 overflow-visible pointer-events-auto">
        
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-2xl overflow-hidden">
            <div class="h-16 bg-gradient-to-r from-blue-500 to-indigo-600"></div>

            <div class="px-5 pb-5">
                <div class="flex justify-between items-end -mt-8 mb-3">
                    <div class="p-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
                        <x-user-avatar :src="$user->profile_picture_url" size="lg" class="rounded-xl" />
                    </div>
                    <a href="{{ route('profiles.show', $user->username) }}" 
                       class="mb-1 px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-full shadow-md transition">
                        View Profile
                    </a>
                </div>

                <div class="mb-3">
                    <h4 class="text-lg font-black text-gray-900 dark:text-white leading-tight">
                        {{ $user->display_name }}
                    </h4>
                    <p class="text-sm text-gray-500">
                        {{ '@' . $user->username }}
                    </p>
                </div>

                @if($user->bio)
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 leading-relaxed">
                        {{ $user->bio }}
                    </p>
                @endif

                <div class="flex gap-6 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ number_format($user->followers_count ?? 0) }}
                        </span>
                        <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Followers</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ number_format($user->posts_count ?? 0) }}
                        </span>
                        <span class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Posts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tooltip Triangle Pointer --}}
        <div class="absolute -bottom-1 left-6 w-3 h-3 bg-white dark:bg-gray-800 border-r border-b border-gray-200 dark:border-gray-700 rotate-45"></div>
    </div>
</div>