<div class="relative" x-data="{ open: false }">

    {{-- Bell Icon --}}
    <button
        @click="open = !open"
        class="relative p-2 rounded hover:bg-gray-100 dark:hover:bg-gray-700">
        🔔

        @if ($unreadCount > 0)
        <span
            class="absolute -top-1 -right-1 text-xs bg-red-600 text-white rounded-full px-1">
            {{ $unreadCount }}
        </span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div
        x-show="open"
        @click.outside="open = false"
        x-transition
        class="absolute right-0 mt-2 w-80
               bg-white dark:bg-gray-800
               border border-gray-200 dark:border-gray-700
               shadow-lg rounded-lg z-50">

        {{-- Header --}}
        <div class="p-3 border-b dark:border-gray-700 flex justify-between">
            <span class="font-semibold text-sm">Notifications</span>

            <a href="{{ route('notifications.index') }}"
                class="text-xs text-blue-600 hover:underline">
                View all
            </a>
        </div>

        {{-- List --}}
        <div class="max-h-96 overflow-y-auto">

            @forelse ($notifications as $notification)
            <div
                class="p-3 border-b dark:border-gray-700
                           {{ $notification->is_read ? 'opacity-60' : 'bg-blue-50/50' }}">
                <div class="flex items-start gap-2 text-sm">
                    <span class="text-lg leading-none">
                        {{ $notification->presenter()->icon() }}
                    </span>

                    <a
                        href="{{ route('notifications.visit', $notification) }}"
                        class="hover:underline">
                        {{ $notification->presenter()->message() }}
                    </a>
                </div>

                <div class="text-xs text-gray-500 mt-1 ml-6">
                    {{ $notification->created_at->diffForHumans() }}
                </div>

                @if (! $notification->is_read)
                <form
                    method="POST"
                    action="{{ route('notifications.read', $notification) }}"
                    class="mt-2 ml-6">
                    @csrf
                    <button class="text-xs text-blue-600 hover:underline">
                        Mark as read
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="p-4 text-sm text-gray-500 text-center">
                No notifications
            </div>
            @endforelse

        </div>
    </div>

</div>