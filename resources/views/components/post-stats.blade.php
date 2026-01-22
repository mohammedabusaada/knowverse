@props(['views', 'comments', 'votes'])

<div class="flex justify-between items-center text-sm text-gray-500 dark:text-gray-400">
    <span>👁 {{ $views }}</span>
    <span>💬 {{ $comments }}</span>
    <span>⬆ {{ $votes }}</span>
</div>