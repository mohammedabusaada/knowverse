@extends('settings._layout')

@section('settings-content')
    <div class="mb-8 border-b border-rule pb-4">
        <h1 class="font-heading text-3xl font-bold text-ink">Notifications Preferences</h1>
        <p class="font-serif text-[15px] italic text-muted mt-2">
            Configure the notifications you wish to receive from the community.
        </p>
    </div>

    @if (session('success'))
        <x-alert type="success">{{ session('success') }}</x-alert>
    @endif

    <form method="POST" action="{{ route('settings.notifications.update') }}" class="space-y-12">
        @csrf
        @method('PUT')

        @foreach ($categories as $categoryName => $types)
            @php
                $visibleTypes = array_filter($types, fn($t) => !$t->isMandatory());
            @endphp

            @if(count($visibleTypes) > 0)
                <section>
                    <h3 class="text-[10px] font-mono font-bold uppercase tracking-[0.2em] text-muted mb-5 border-b border-rule pb-2">
                        {{ ucfirst($categoryName) }}
                    </h3>

                    <div class="space-y-6">
                        @foreach ($visibleTypes as $type)
                            @php
                                // Fetch the human-readable label from the config file
                                $label = config("notification-preferences.categories.{$type->value}.label", str_replace('_', ' ', ucfirst($type->value)));
                            @endphp
                            <div class="flex items-center justify-between group">
                                <div class="pr-6">
                                    <label for="pref_{{ $type->value }}" class="text-base font-serif font-bold text-ink cursor-pointer block mb-1 group-hover:text-accent-warm transition-colors">
                                        {{ $label }}
                                    </label>
                                </div>

                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input 
                                        type="checkbox" 
                                        id="pref_{{ $type->value }}"
                                        name="preferences[{{ $type->value }}]" 
                                        value="1" 
                                        class="sr-only peer"
                                        {{ $user->notificationEnabled($type) ? 'checked' : '' }}
                                    >
                                    <div class="w-11 h-6 bg-rule rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-paper after:border after:border-rule after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ink"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach

        <div class="pt-8 border-t border-rule flex justify-end">
            <button type="submit" class="px-8 py-3 bg-ink text-paper font-mono text-[10px] uppercase tracking-widest hover:opacity-80 transition-opacity shadow-sm">
                Save Preferences
            </button>
        </div>
    </form>
@endsection