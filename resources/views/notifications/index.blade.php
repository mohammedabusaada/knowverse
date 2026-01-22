@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Notifications</h1>

        <div class="flex items-center gap-4">
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="text-sm text-blue-600 hover:underline">
                    Mark all as read
                </button>
            </form>

            <form method="POST"
                action="{{ route('notifications.clear') }}"
                onsubmit="return confirm('Clear all notifications?');">
                @csrf
                @method('DELETE')
                <button class="text-sm text-red-600 hover:underline">
                    Clear all
                </button>
            </form>
        </div>
    </div>

    {{-- Notifications --}}
    @forelse ($notifications as $notification)
    <div
        x-data="{ seen: false }"
        x-intersect.once="
                if (!seen && {{ $notification->is_read ? 'false' : 'true' }}) {
                    seen = true;
                    fetch('{{ route('notifications.read', $notification) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                }
            "
        class="border-b py-4 transition
                   {{ $notification->is_read ? 'opacity-60' : 'bg-blue-50/50' }}">
        <div class="flex items-start gap-3 text-sm">
            <span class="text-lg leading-none">
                {{ $notification->presenter()->icon() }}
            </span>

            <a
                href="{{ route('notifications.visit', $notification) }}"
                class="hover:underline">
                {{ $notification->presenter()->message() }}
            </a>
        </div>

        <div class="text-xs text-gray-500 mt-1 ml-8">
            {{ $notification->created_at->diffForHumans() }}
        </div>
    </div>
    @empty
    <p class="text-gray-500">No notifications yet.</p>
    @endforelse

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>

</div>
@endsection