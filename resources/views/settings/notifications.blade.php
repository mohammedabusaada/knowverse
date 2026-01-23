@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold mb-6 dark:text-white">
        Notification Preferences
    </h1>

    @if (session('success'))
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('settings.notifications.update') }}">
        @csrf

        <div class="space-y-4">
            @foreach ($categories as $type => $config)
    <label class="flex items-center gap-3">
        <input
            type="checkbox"
            name="preferences[{{ $type }}]"
            value="1"
            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            {{ ($preferences->get($type)?->enabled ?? $config['default']) ? 'checked' : '' }}
        >
        <span class="text-gray-800 dark:text-gray-200">
            {{ $config['label'] }}
        </span>
    </label>
@endforeach

        </div>

        <button
            type="submit"
            class="mt-6 px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            Save
        </button>
    </form>
</div>
@endsection
