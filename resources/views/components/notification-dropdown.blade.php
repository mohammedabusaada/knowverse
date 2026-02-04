<div
    x-data="{
        open: false,
        loading: false,
        toggle() {
            this.open = !this.open;

            if (this.open) {
                this.loading = true;
                setTimeout(() => (this.loading = false), 500);
            }
        }
    }"
    x-init="
        // منع تمرير الصفحة عند الفتح + إرجاعه عند الإغلاق
        $watch('open', v => document.body.classList.toggle('overflow-hidden', v));
    "
    @keydown.escape.window="open = false"
    class="relative z-[10000]"  {{-- 🔥 الجرس فوق البلور --}}
>
    {{-- Trigger (Bell) --}}
<button
    type="button"
    @click="toggle()"
    class="relative z-[10001] p-2 rounded-lg
           text-gray-700 dark:text-gray-200
           hover:text-gray-900 dark:hover:text-white
           hover:bg-gray-100 dark:hover:bg-gray-800
           transition-colors focus:outline-none"
    aria-label="Notifications"
    :aria-expanded="open.toString()"
>


        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center
                         min-w-[18px] h-[18px] px-1
                         text-[10px] font-bold text-white
                         bg-blue-600
                         border-2 border-white dark:border-gray-950
                         rounded-full">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- ✅ TELEPORT: نخلي البلور والبانل داخل body عشان يغطي كل الصفحة --}}
    <template x-teleport="body">
        <div>
            {{-- Backdrop (يغطي كل الصفحة) --}}
            <div
                x-show="open"
                x-transition.opacity
                x-cloak
                @click="open = false"
                class="fixed inset-0 z-[9000]
                       bg-black/40 backdrop-blur-sm"
                aria-hidden="true"
            ></div>

            {{-- Panel (فوق البلور، لكن تحت الجرس) --}}
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-cloak
                class="fixed z-[9500] w-80
                       bg-white dark:bg-gray-900
                       border border-gray-200 dark:border-gray-800
                       shadow-2xl rounded-xl overflow-hidden"
                style="top: 72px; right: 24px;"
                role="menu"
            >
                {{-- Header --}}
                <div class="p-4 border-b border-gray-200 dark:border-gray-800
                            bg-gray-50 dark:bg-gray-900
                            flex justify-between items-center">
                    <h3 class="text-xs font-bold text-gray-900 dark:text-gray-100 uppercase tracking-wider">
                        Notifications
                    </h3>

                    <button
                        type="button"
                        class="text-xs font-semibold
                               text-blue-600 dark:text-blue-400
                               hover:underline"
                    >
                        Mark all read
                    </button>
                </div>

                {{-- Loading skeleton --}}
                <div x-show="loading" class="p-4 space-y-3 animate-pulse">
                    <div class="h-4 bg-gray-200 dark:bg-gray-800 rounded w-3/4"></div>
                    <div class="h-3 bg-gray-100 dark:bg-gray-850 rounded w-1/2"></div>
                </div>

                {{-- List --}}
                <div x-show="!loading" class="max-h-[400px] overflow-y-auto">
                    @forelse ($notifications as $notification)
                        @include('notifications.partials.notification-item', ['notification' => $notification])
                    @empty
                        <div class="p-10 text-center">
                            <p class="text-sm text-gray-500 dark:text-gray-400">All caught up! 🎉</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="p-3 text-center border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <a href="{{ route('notifications.index') }}"
                       class="text-sm font-medium
                              text-blue-600 dark:text-blue-400
                              hover:underline">
                        See all notifications
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>
