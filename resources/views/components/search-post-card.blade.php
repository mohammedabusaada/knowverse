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
            '<mark class="bg-yellow-200 dark:bg-yellow-600 px-1 rounded">$0</mark>',
            $escaped
        );
    } else {
        $highlighted = $escaped;
    }
@endphp

<a href="{{ route('posts.show', $post) }}"
   class="block p-5 bg-white dark:bg-gray-800 rounded-xl
          border border-gray-200 dark:border-gray-700
          hover:shadow-md transition">

    {{-- Header --}}
    <div class="flex justify-between items-start gap-4">
        <div>
            <h3 class="text-lg font-semibold dark:text-white">
                {{ $post->title }}
            </h3>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                by {{ $post->user->display_name }}
                · {{ $post->created_at->diffForHumans() }}
            </p>
        </div>

        {{-- Views --}}
        <div class="text-sm text-gray-500 dark:text-gray-400 shrink-0">
            👁 {{ $post->view_count }}
        </div>
    </div>

    {{-- Snippet --}}
    <p class="mt-3 text-gray-700 dark:text-gray-300 prose max-w-none">
        {!! $highlighted !!}
    </p>
</a>
