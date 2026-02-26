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
            this.timer = setTimeout(() => { this.open = false }, 300);
        }
    }"
    class="relative inline-block"
    @mouseenter="show()"
    @mouseleave="hide()"
>
    <a
        href="{{ route('profile.show', $user->username) }}"
        class="font-bold text-black dark:text-white hover:underline decoration-2 underline-offset-2 transition"
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
                    border-2 border-black dark:border-white
                    rounded-2xl shadow-2xl overflow-hidden">

            <div class="p-5">
                <div class="flex justify-between items-start gap-3 mb-4">
                    <div class="flex items-start gap-3 min-w-0">
                        <x-user-avatar :src="$user->profile_picture_url" size="lg" class="rounded-xl border-2 border-black dark:border-white shadow-sm" />

                        <div class="min-w-0">
                            <h4 class="text-base font-black text-gray-900 dark:text-gray-100 leading-tight truncate">
                                {{ $user->display_name }}
                            </h4>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                {{ '@' . $user->username }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($user->bio)
                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-5 leading-relaxed">
                        {{ $user->bio }}
                    </p>
                @endif

                <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-800">
                    <div class="flex gap-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-black dark:text-white">
                                {{ number_format($user->followers_count ?? 0) }}
                            </span>
                            <span class="text-[9px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-widest">
                                Followers
                            </span>
                        </div>

                        <div class="flex flex-col">
                            <span class="text-sm font-black text-black dark:text-white">
                                {{ number_format($user->posts_count ?? 0) }}
                            </span>
                            <span class="text-[9px] text-gray-500 dark:text-gray-400 uppercase font-bold tracking-widest">
                                Posts
                            </span>
                        </div>
                    </div>

                    {{-- Dynamic Follow Button inside hover card --}}
                    @if(auth()->check() && auth()->id() !== $user->id)
                        <div class="shrink-0 scale-90 origin-right">
                            <x-follow-button :user="$user" />
                        </div>
                    @else
                        <a href="{{ route('profile.show', $user->username) }}" class="text-xs font-bold text-gray-500 hover:text-black dark:hover:text-white transition">View Profile</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pointer --}}
        <div class="absolute -bottom-1.5 left-6 w-3.5 h-3.5
                    bg-white dark:bg-gray-900
                    border-r-2 border-b-2 border-black dark:border-white
                    rotate-45"></div>
    </div>
</div>