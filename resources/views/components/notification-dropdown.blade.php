<div
    x-data="{
        open: false,
        loading: false,
        unreadCount: {{ $unreadCount ?? 0 }},
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.loading = true;
                setTimeout(() => (this.loading = false), 400);
            }
        }
    }"
    x-init="$watch('open', v => document.body.classList.toggle('overflow-hidden', v));"
    @keydown.escape.window="open = false"
    @realtime-notification.window="unreadCount++"
    class="relative z-[10000]"
>
    {{-- Trigger (Bell Icon) --}}
    <button
        type="button"
        @click="toggle()"
        class="relative z-[10001] p-2 rounded-sm text-muted hover:text-ink hover:bg-aged transition-colors focus:outline-none"
        aria-label="Notifications"
        :aria-expanded="open.toString()"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        {{-- Dynamic Badge --}}
        <span x-show="unreadCount > 0"
              x-text="unreadCount"
              x-cloak
              class="absolute top-0 right-0 flex items-center justify-center min-w-[16px] h-[16px] px-1 font-mono text-[9px] font-bold text-paper bg-accent-warm rounded-sm transition-all duration-300 transform scale-100">
        </span>
    </button>

    {{-- TELEPORT: Panel Rendering --}}
    <template x-teleport="body">
        <div>
            {{-- Backdrop Overlay --}}
            <div x-show="open" x-transition.opacity x-cloak @click="open = false" class="fixed inset-0 z-[9000] bg-ink/5 backdrop-blur-sm" aria-hidden="true"></div>

            {{-- Notification Dropdown Panel --}}
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
                class="fixed z-[9500] w-[90vw] max-w-sm sm:w-96 bg-paper border border-rule shadow-2xl rounded-sm overflow-hidden"
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
                        <button type="submit" class="font-mono text-[9px] text-ink hover:text-accent-warm uppercase tracking-widest transition-colors focus:outline-none">
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
                <div x-show="!loading" class="max-h-[60vh] overflow-y-auto scrollbar-hide" id="notification-list">
                    @forelse ($notifications as $notification)
                        @include('notifications.partials.notification-item', ['notification' => $notification])
                    @empty
                        <div class="p-12 text-center" id="empty-notifications-msg">
                            <p class="font-serif text-sm text-muted italic">You have no new notifications.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer with Inline Confirmation --}}
                <div class="px-5 py-3 border-t border-rule bg-aged/30 flex justify-between items-center" x-data="{ confirmingClear: false }">
                    
                    {{-- Navigation Link --}}
                    <a href="{{ route('notifications.index') }}" class="font-mono text-[10px] uppercase tracking-[0.1em] text-ink hover:text-accent transition-colors focus:outline-none">
                        View All &rarr;
                    </a>

                    {{-- Destructive Actions Container --}}
                    <div>
                        {{-- Default State: Clear Button --}}
                        <button type="button" x-show="!confirmingClear" @click="confirmingClear = true" class="font-mono text-[9px] uppercase tracking-widest text-accent-warm hover:opacity-70 transition-opacity focus:outline-none">
                            Clear
                        </button>

                        {{-- Confirmation State: Inline Are You Sure? --}}
                        <div x-show="confirmingClear" x-cloak class="flex items-center gap-3">
                            <span class="font-serif text-[10px] text-muted italic">Are you sure?</span>
                            
                            <button type="button" @click="confirmingClear = false" class="font-mono text-[9px] uppercase tracking-widest text-ink hover:opacity-70 transition-opacity focus:outline-none">
                                No
                            </button>
                            
                            <form method="POST" action="{{ route('notifications.clear') }}" class="inline-block m-0 p-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="font-mono text-[9px] uppercase tracking-widest text-accent-warm hover:opacity-70 transition-opacity focus:outline-none">
                                    Yes
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </template>
</div>