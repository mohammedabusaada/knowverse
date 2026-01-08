@props(['activity'])

@php
    use App\Support\ActivityPresenter;

    $title    = ActivityPresenter::title($activity);
    $color    = ActivityPresenter::color($activity);
    $link     = ActivityPresenter::link($activity);
    $linkText = ActivityPresenter::linkText($activity);
@endphp

<div class="flex gap-4 py-4 border-b dark:border-gray-700">

    {{-- Timeline dot --}}
    <div class="flex flex-col items-center">
        <span class="w-3 h-3 rounded-full bg-blue-600 mt-1"></span>
        <span class="flex-1 w-px bg-gray-300 dark:bg-gray-700"></span>
    </div>

    {{-- Content --}}
    <div class="flex-1">

        <div class="text-sm {{ $color }}">
            <span class="font-medium">{{ $title }}</span>

            @if ($link && $linkText)
                —
                <a href="{{ $link }}"
                   class="text-blue-600 dark:text-blue-400 hover:underline">
                    {{ $linkText }}
                </a>
            @endif

            @if ($activity->details)
                <span class="text-gray-500">
                    {{ $activity->details }}
                </span>
            @endif
        </div>

        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ $activity->created_at->diffForHumans() }}
        </div>

    </div>

</div>
