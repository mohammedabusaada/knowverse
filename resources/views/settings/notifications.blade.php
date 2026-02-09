@extends('settings._layout')

@section('settings-content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notification Preferences</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Control which activities trigger a notification alert.
        </p>
    </div>

    @if (session('success'))
        <x-alert type="success" class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-10">
        @csrf
        @method('PUT')

        @foreach ($categories as $categoryName => $types)
            @php
                // Filter out mandatory types from the UI
                $visibleTypes = array_filter($types, fn($t) => !$t->isMandatory());
            @endphp

            @if(count($visibleTypes) > 0)
                <section>
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-4 border-b border-gray-100 dark:border-gray-800 pb-2">
                        {{ ucfirst($categoryName) }}
                    </h3>

                    <div class="space-y-4">
                        @foreach ($visibleTypes as $type)
                            <div class="flex items-center justify-between group">
                                <div>
                                    <label for="pref_{{ $type->value }}" class="font-medium text-gray-800 dark:text-gray-200 cursor-pointer">
                                        {{ str_replace('_', ' ', ucfirst($type->value)) }}
                                    </label>
                                    <p class="text-xs text-gray-500">Receive alerts for this activity</p>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        id="pref_{{ $type->value }}"
                                        name="preferences[{{ $type->value }}]" 
                                        value="1" 
                                        class="sr-only peer"
                                        {{ $user->notificationEnabled($type) ? 'checked' : '' }}
                                    >
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 
                                        peer-checked:after:translate-x-full peer-checked:after:border-white 
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px] 
                                        after:bg-white after:border-gray-300 after:border after:rounded-full 
                                        after:h-5 after:w-5 after:transition-all dark:border-gray-600 
                                        peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
            <x-button type="submit" primary>
                Save Preferences
            </x-button>
        </div>
    </form>
@endsection