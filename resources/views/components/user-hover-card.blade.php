@props(['user'])

<div
    x-data="{
        open: false,
        timer: null,
        show() {
            clearTimeout(this.timer);
            this.open = true;
        },
        hide() {
            // 300ms delay allows moving into the card
            this.timer = setTimeout(() => { this.open = false }, 300);
        }
    }"
    class="relative inline-block"
    @mouseenter="show()"
    @mouseleave="hide()"
>
    <a
        href="{{ route('profile.show', $user->username) }}"
        class="font-semibold text-blue-600 dark:text-blue-400 hover:underline decoration-2 underline-offset-2 transition"
    >
        {{ $user->display_name }}
    </a>

    <div
        x-show="open"
        @mouseenter="show()"
        @mouseleave="hide()"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-cloak
        class="absolute bottom-full left-0 z-[9999] pt-3 w-80 overflow-visible pointer-events-auto"
    >
        <div class="bg-white dark:bg-gray-900
                    border border-gray-200 dark:border-gray-800
                    rounded-2xl shadow-lg overflow-hidden">

            {{-- Top accent line (instead of gradient) --}}
            <div class="h-1 bg-blue-600/70 dark:bg-blue-500/70"></div>

            <div class="px-5 pb-5 pt-4">
                <div class="flex justify-between items-start gap-3 mb-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="p-1 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800">
                            <x-user-avatar :src="$user->profile_picture_url" size="lg" class="rounded-xl" />
                        </div>

                        <div class="min-w-0">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 leading-tight truncate">
                                {{ $user->display_name }}
                            </h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                {{ '@' . $user->username }}
                            </p>
                        </div>
                    </div>

                    <a
                        href="{{ route('profile.show', $user->username) }}"
                        class="shrink-0 inline-flex items-center justify-center
                               px-3 py-1.5 rounded-lg text-xs font-semibold
                               bg-blue-600 text-white hover:bg-blue-700
                               dark:bg-blue-500 dark:hover:bg-blue-600
                               transition"
                    >
                        View Profile
                    </a>
                </div>

                @if($user->bio)
                    <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 mb-4 leading-relaxed">
                        {{ $user->bio }}
                    </p>
                @endif

                <div class="flex gap-6 pt-4 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($user->followers_count ?? 0) }}
                        </span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-widest">
                            Followers
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ number_format($user->posts_count ?? 0) }}
                        </span>
                        <span class="text-[10px] text-gray-500 dark:text-gray-400 uppercase font-semibold tracking-widest">
                            Posts
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pointer --}}
        <div class="absolute -bottom-1 left-6 w-3 h-3
                    bg-white dark:bg-gray-900
                    border-r border-b border-gray-200 dark:border-gray-800
                    rotate-45"></div>
    </div>
</div>
