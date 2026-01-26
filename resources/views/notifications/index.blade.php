@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Notifications</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Stay updated with your latest interactions.</p>
        </div>

        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                    Mark all read
                </button>
            </form>

            <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Clear all?');">
                @csrf @method('DELETE')
                <button class="inline-flex items-center px-3 py-1.5 text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                    Clear all
                </button>
            </form>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-2xl shadow-sm overflow-hidden">
        @forelse ($notifications as $notification)
        @include('notifications.partials.notification-item', ['notification' => $notification])
        @empty
        <div class="py-20 text-center">
            <div class="text-5xl mb-4">🔔</div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No notifications yet</h3>
            <p class="text-gray-500 dark:text-gray-400">We'll let you know when something happens.</p>
        </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
</div>
@endsection