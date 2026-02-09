@props(['type', 'points', 'date', 'source' => null, 'sourceType' => null])

@php
    $isPositive = $points > 0;
    
    // Map database 'action' strings to UI styles
    $styles = match($type) {
        'post_upvote', 'upvote' => ['icon' => 'arrow-up', 'color' => 'text-green-600', 'bg' => 'bg-green-50 dark:bg-green-900/20'],
        'post_downvote', 'downvote' => ['icon' => 'arrow-down', 'color' => 'text-red-600', 'bg' => 'bg-red-50 dark:bg-red-900/20'],
        'comment_upvote' => ['icon' => 'chat', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
        'award' => ['icon' => 'chart', 'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20'],
        default => ['icon' => 'user', 'color' => 'text-gray-600', 'bg' => 'bg-gray-50 dark:bg-gray-800'],
    };

    // Determine the link based on the source
    $url = null;
    if ($source) {
        if ($sourceType === \App\Models\Post::class) {
            $url = route('posts.show', $source);
        } elseif ($sourceType === \App\Models\Comment::class && isset($source->post_id)) {
            $url = route('posts.show', $source->post_id) . '#comment-' . $source->id;
        }
    }
@endphp

<div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
    <div class="flex items-center gap-4">
        <div class="p-2.5 {{ $styles['bg'] }} {{ $styles['color'] }} rounded-xl">
            <x-dynamic-component :component="'icons.' . $styles['icon']" class="w-5 h-5" />
        </div>
        <div>
            <p class="text-sm font-bold text-gray-900 dark:text-white">
                {{ str_replace('_', ' ', ucwords($type, '_')) }}
                @if($url)
                    <a href="{{ $url }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium ml-1">
                        View source
                    </a>
                @endif
            </p>
            <p class="text-xs text-gray-500 font-medium">
                {{ $date }}
            </p>
        </div>
    </div>
    
    <div class="text-sm font-black {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
        {{ $isPositive ? '+' : '' }}{{ $points }}
    </div>
</div>