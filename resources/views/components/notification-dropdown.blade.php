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
        // Prevent body scroll when the dropdown is open
        $watch('open', v => document.body.classList.toggle('overflow-hidden', v));
    "
    @keydown.escape.window="open = false"
    class="relative z-[10000]"
>
    {{-- Trigger (Bell Icon) --}}
    <button
        type="button"
        @click="toggle()"
        class="relative z-[10001] p-2 rounded-sm
               text-muted hover:text-ink
               hover:bg-aged transition-colors focus:outline-none"
        aria-label="Notifications"
        :aria-expanded="open.toString()"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute top-0 right-0 flex items-center justify-center
                         min-w-[16px] h-[16px] px-1
                         font-mono text-[9px] font-bold text-paper
                         bg-accent
                         rounded-sm">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- TELEPORT: Move panel to body to prevent stacking issues --}}
    <template x-teleport="body">
        <div>
            {{-- Backdrop --}}
            <div
                x-show="open"
                x-transition.opacity
                x-cloak
                @click="open = false"
                class="fixed inset-0 z-[9000] bg-ink/5 backdrop-blur-sm"
                aria-hidden="true"
            ></div>

            {{-- Notification Panel --}}
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                x-cloak
                class="fixed z-[9500] w-[90vw] max-w-sm sm:w-96
                       bg-paper border border-rule shadow-2xl rounded-sm overflow-hidden"
                style="top: 72px; right: max(24px, calc(50vw - 32rem + 24px));"
                role="menu"
            >
                {{-- Header --}}
                <div class="px-5 py-3 border-b border-rule bg-aged/30 flex justify-between items-center">
                    <h3 class="font-mono text-[10px] font-bold text-muted uppercase tracking-[0.2em]">
                        Notifications
                    </h3>
                    <form method="POST" action="{{ route('notifications.readAll') }}">
                        @csrf
                        <button type="submit" class="font-mono text-[9px] text-accent hover:text-ink uppercase tracking-widest transition-colors focus:outline-none">
                            Mark all read
                        </button>
                    </form>
                </div>

                {{-- Loading Skeleton --}}
                <div x-show="loading" class="p-5 space-y-4 animate-pulse">
                    <div class="h-3 bg-rule/50 rounded-sm w-3/4"></div>
                    <div class="h-2 bg-rule/30 rounded-sm w-1/2"></div>
                </div>

                {{-- Notification List --}}
                <div x-show="!loading" class="max-h-[60vh] overflow-y-auto">
                    @forelse ($notifications as $notification)
                        @include('notifications.partials.notification-item', ['notification' => $notification])
                    @empty
                        <div class="p-12 text-center">
                            <p class="font-serif text-sm text-muted italic">No new correspondence.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div class="px-5 py-3 text-center border-t border-rule bg-aged/30 flex justify-between items-center">
                    <a href="{{ route('notifications.index') }}" class="font-mono text-[10px] uppercase tracking-[0.1em] text-muted hover:text-ink transition-colors focus:outline-none">
                        View Archive &rarr;
                    </a>
                    <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Clear all?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="font-mono text-[9px] uppercase tracking-widest text-[#a65a38] hover:opacity-70 transition-opacity">
                            Clear
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>