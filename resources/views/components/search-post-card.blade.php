@props([
    'post',
    'q' => '',
])

@php
    $snippet = Str::limit(strip_tags($post->body), 220);
    $escaped = e($snippet);

    if ($q !== '') {
        $pattern = '/' . preg_quote($q, '/') . '/i';
        $highlighted = preg_replace(
            $pattern,
            '<mark class="bg-blue-600/10 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300 px-1 rounded-sm">$0</mark>',
            $escaped
        );
    } else {
        $highlighted = $escaped;
    }
@endphp

<a
    href="{{ route('posts.show', $post) }}"
    class="block p-5
           bg-white dark:bg-gray-900
           border border-gray-200 dark:border-gray-800
           rounded-xl
           hover:border-gray-300 dark:hover:border-gray-700
           transition"
>
    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                {{ $post->title }}
            </h3>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                by <span class="font-medium text-gray-700 dark:text-gray-300">{{ $post->user->display_name }}</span>
                <span class="mx-1 text-gray-300 dark:text-gray-700">•</span>
                {{ $post->created_at->diffForHumans() }}
            </p>
        </div>

        {{-- Views --}}
        <div class="shrink-0 text-xs text-gray-500 dark:text-gray-400 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span>{{ number_format($post->view_count) }}</span>
        </div>
    </div>

    {{-- Snippet --}}
    <p class="mt-3 text-sm leading-relaxed text-gray-700 dark:text-gray-300">
        {!! $highlighted !!}
    </p>

    {{-- Subtle footer (optional info) --}}
    <div class="mt-4 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
        <span class="inline-flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-300 dark:bg-gray-700"></span>
            Open discussion
        </span>

        <span class="text-blue-600/70 dark:text-blue-400/70 font-semibold">
            View →
        </span>
    </div>
</a>
